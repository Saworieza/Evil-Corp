<?php
use custom\helpers\FileProcesor;
use custom\helpers\Midrepo;
use custom\helpers\Responder;
use custom\exceptions\ApiException;

class AttachmentController extends BaseController
{

    /**
     * @param $code
     * @param null $preview
     * @return \Illuminate\Http\Response
     */
    public function getFile($code, $preview = null)
    {
        $file = Midrepo::getOrFail('file');

        if ($file->file_type == 'image') {
            $pr       = isset($preview) ? 'p_' : '';
            $fileName = filesFolder() . $pr . $code . '_' . $file->file_name;

        } else {
            if (isset($preview)) {
                $fileName = getFileTypeIcon($file->file_ext);
            } else {
                $fileName = filesFolder() . $code . '_' . $file->file_name;
            }
        }

        if (!file_exists($fileName)) {
            App::abort(404);
        }

        $mime = getMimeType(pathinfo($fileName, PATHINFO_EXTENSION));

        $response = Response::make(File::get($fileName));
        $response->header('Content-Type', $mime);
        $response->header('Content-Disposition', 'inline; filename="' . $file->file_name);
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Cache-Control', 'public, max-age=10800, pre-check=10800');
        $response->header('Pragma', 'public');
        $response->header('Expires', date(DATE_RFC822, strtotime(" 2 day")));
        $response->header('Last-Modified', date(DATE_RFC822, File::lastModified($fileName)));
        $response->header('Content-Length', File::size($fileName));
        return $response;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload()
    {
        if (!Input::hasFile('file')) {
            return Responder::json(false)->withData(['id' => Input::get('id')])->send();
        }

        $fp = new FileProcesor(Input::file('file'));

        if ($fp->isImage()) {
            $maxRes = Config::get('app.max_image_resolution');
            if ($maxRes[0] > 0 && $maxRes[1] > 0) {
                $fp->imageResize($maxRes[0], $maxRes[1]);
            }
            $fp->genPreview();
        }

        $fileDate = substr(Input::get('filedate'), 1, strlen(Input::get('filedate')) - 2);

        $att              = new Attachment;
        $att->file_name   = $fp->getFileName();
        $att->code        = $fp->code;
        $att->file_size   = $fp->getSize();
        $att->file_ext    = $fp->getExtension();
        $att->description = Input::get('description', '');
        $att->file_date   = $fileDate;
        $att->user_id     = Auth::user()->id;
        if (Input::has('wiki_id')) {
            $att->attachable_type = 'Wiki';
            $att->attachable_id   = Input::get('wiki_id');
        }
        $att->save();

        $ret = [
            'code'      => $att->code,
            'id'        => Input::get('id'),
            'file_name' => $att->file_name,
            'file_date' => $att->file_date,
            'file_size' => $att->file_size,
            'icon'      => getFileTypeIconUrl($att->file_ext)

        ];

        return Responder::json(true)->withData($ret)->send();

    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($code)
    {
        $file = Midrepo::getOrFail('file');

        $file->delete();

        return Responder::json(true)->send();

    }

    /**
     * @param $code
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($code, $folderId)
    {

        $file = Midrepo::getOrFail('file');

        $file->attachable_id = $folderId;
        $file->save();

        return Responder::json(true)->send();

    }

    /**
     * @param $code
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function copy($code, $folderId)
    {
        $file = Midrepo::getOrFail('file');

        $newFile                  = $file->replicate(['id', 'code']);
        $newFile->code            = md5(date('Ymdhis') . rand(100, 999));
        $newFile->attachable_type = 'Folder';
        $newFile->attachable_id   = $folderId;
        $newFile->save();

        copy(
            filesFolder() . $file->code . '_' . $file->file_name,
            filesFolder() . $newFile->code . '_' . $file->file_name
        );
        return Responder::json(true)->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function getList()
    {
        $page      = Input::get('page', 0);
        $page      = ' LIMIT ' . ($page * 50) . ',50 ';
        $sortField = Input::get('sort', 'file_name');
        if ($sortField == 'full_name') {
            $sortField = 'full_name';
        }
        if (Input::has('folder_id')) {
            $spaceId = Folder::findOrFail(Input::get('folder_id'))->space_id;
            if (Auth::user()->inSpace($spaceId) < ROLE_MEMBER) {
                throw new ApiException('access_denied');
            }

            $files = Attachment::join('users', 'attachments.user_id', '=', 'users.id')
                ->where('attachable_id', Input::get('folder_id'))
                ->where('attachable_type', 'Folder')
                ->skip(Input::get('page', 0) * 50)
                ->take(50)
                ->select('attachments.*', 'users.full_name')
                ->orderBy($sortField, Input::get('asc') == 'true' ? 'asc' : 'desc');

            $filter = Input::get('filter', '');
            if ($filter != '') {
                $filter = '%' . $filter . '%';
                $files  = $files->where('file_name', 'LIKE', $filter)
                    ->orWhere('description', 'LIKE', $filter);
            }

            $files = $files->get()->toArray();

            // dd($files);
            return Responder::json(true)->withDataTransform($files, 'FileTransformer')->send();
        }

        if (Input::has('space_code')) {
            $spaceId = Space::getByCode(Input::get('space_code'))->id;

            if (Auth::user()->inSpace($spaceId) < ROLE_MEMBER) {
                return Responder::json(false)->send();
            }

            $order  = Input::get('sort', 'file_name');
            $order  = $order . (Input::get('asc') == 'true' ? ' ASC' : ' DESC');
            $filter = Input::get('filter', '');
            if ($filter != '') {
                $filter = " where file_name LIKE '%" . $filter . "%' OR file_name LIKE '%" . $filter . "%'";
            }

            $contentFiles = DB::select(
                DB::raw(
                    "SELECT att.*, u.full_name from attachments att " .
                    "join users u on (att.user_id = u.id) " .
                    "join content c on (att.attachable_type = 'Content' and att.`attachable_id` = c.id and c.space_id =$spaceId)" . $filter .
                    " order by $order" . $page
                )
            );

            $commentFiles = DB::select(
                DB::raw(
                    "SELECT att.*, u.full_name from attachments att " .
                    "join users u on (att.user_id = u.id) " .
                    "join comments cm on (att.attachable_type = 'Comment' and att.`attachable_id` = cm.id) " .
                    "join content co on (cm.commentable_type = 'Content' and cm.commentable_id = co.id and co.space_id=$spaceId)" . $filter .
                    " order by $order" . $page
                )
            );

            $files = array_merge($contentFiles, $commentFiles);

            $files = json_decode(json_encode($files), true);

            usort(
                $files, function ($a, $b) {

                    if (Input::get('asc') == 'true') {
                        return strtolower($b[Input::get('sort')]) < strtolower($a[Input::get('sort')]);
                    } else {
                        return strtolower($a[Input::get('sort')]) < strtolower($b[Input::get('sort')]);
                    }

                }
            );

            if (Input::has('view')) {
                $files = array_splice($files, 0, 5);
            }

            return Responder::json(true)->withDataTransform($files, 'FileTransformer')->send();

        }

        return Responder::json(false)->send();

    }

}
