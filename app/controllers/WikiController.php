<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;
use custom\exceptions\ApiException;

/**
 * Class WikiController
 */
class WikiController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $spaceId = Midrepo::getOrFail('space')->id;

        if(Input::has('s'))
        {
            $s = Input::get('s');
            $query = "select wikis.id from ftindex " .
                "join wikis on (ftindex.indexable_id = wikis.id)" .
                "where MATCH(ftindex.body) AGAINST ('$s' in boolean mode)" .
                "and indexable_type = 'Wiki' and wikis.space_id=$spaceId";

            $result = DB::select(DB::raw($query));

            $result = array_pluck($result, 'id');

            $wikis = Wiki::where('space_id', $spaceId)
                ->with('createdBy')
                ->with('updatedBy')
                ->with('attachments')
                ->whereIn('id', $result)
                ->orderBy('title')->get()->toArray();


        } else {

            $wikis = Wiki::where('space_id', $spaceId)
                ->with('createdBy')
                ->with('updatedBy')
                ->with('attachments')
                ->orderBy('title')->get()->toArray();
        }


        return Responder::json(true)->withDataTransform($wikis, 'WikiTransformer')->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBody()
    {
        $wiki = Midrepo::getOrFail('wiki');

        return Responder::json(true)->withData($wiki->body)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne()
    {
        $wiki = Midrepo::getOrFail('wiki');

        return Responder::json(true)->withData($wiki)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function store()
    {
        $wiki             = new Wiki;
        $wiki->title      = Input::get('title');
        $wiki->body       = Input::get('body');
        $wiki->access     = Input::get('access');
        $wiki->created_by = Auth::user()->id;
        $wiki->updated_by = Auth::user()->id;
        $wiki->space_id   = Midrepo::getOrFail('space')->id;
        $wiki->summary    = '';

        if (!$wiki->save()) {
            throw new ApiException($wiki->validator);
        }


        DB::table('ftindex')->insert(
            ['body'=> strip_tags($wiki->body),
             'indexable_id'=> $wiki->id,
             'indexable_type'=>'Wiki']
        );

        Attachment::processInput('Wiki', $wiki->id);

        $wiki = $wiki->load('createdBy')->load('updatedBy')->load('attachments');
        return Responder::json(true)->withDataTransform($wiki, 'WikiTransformer')->send();

    }

    /**
     * @param $wikiId
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function update($wikiId)
    {
        $wiki = Midrepo::getOrFail('wiki');
        if ($wiki->created_by != Auth::user()->id) {
            if ($wiki->access == 'PR' && Auth::user()->inSpace($wiki->space_id) < ROLE_MODERATOR) {
                throw new ApiException("access_denied");
            }
        }

        $wiki->setFromInput('title');
        if (Auth::user()->inSpace($wiki->space_id) == ROLE_MODERATOR || $wiki->created_by == Auth::user()->id) {
            $wiki->setFromInput('access');
        }

        $wiki->setFromInput('body');
        $wiki->updated_by = Auth::user()->id;
        $wiki->save();

        DB::table('ftindex')->where('indexable_id', $wiki->id)->where('indexable_type', 'Wiki')
            ->update(['body'=> strip_tags($wiki->body)]);

        $wiki = $wiki->load('createdBy')->load('updatedBy')->load('attachments');

        return Responder::json(true)->withDataTransform($wiki, 'WikiUpdateTransformer')->send();

    }

    /**
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($folderId)
    {

        DB::table('ftindex')->where('indexable_id', Midrepo::getOrFail('wiki')->id)->where('indexable_type', 'Wiki')->delete();

        $wiki = Midrepo::getOrFail('wiki');
        $wiki->delete();


        return Responder::json(true)->send();


    }


}