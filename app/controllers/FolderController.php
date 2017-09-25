<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;

/**
 * Class FolderController
 */
class FolderController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $spaceId = Midrepo::getOrFail('space')->id;

        $folders = Folder::where('space_id', $spaceId)->orderBy('name')->get()->toArray();

        $res = [];
        tree($folders, $res);

        return Responder::json(true)->withData($res)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $folder           = new Folder;
        $folder->name     = Input::get('name');
        $folder->space_id = Midrepo::getOrFail('space')->id;
        if(Input::get('parent_id') > 0)
            $folder->parent_id = Input::get('parent_id');
        $folder->save();

        return Responder::json(true)->withData($folder)->send();

    }

    /**
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($folderId)
    {
        $folder       = Folder::findOrFail($folderId);
        $folder->name = Input::get('name');
        $folder->save();

        return Responder::json(true)->withData($folder)->send();

    }

    /**
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($folderId)
    {
        $cnt = Folder::where('parent_id', $folderId)->count();
        if($cnt > 0)
            return Responder::json(false)->withMessage('cant_delete_folder')->send();

        $cnt = Attachment::where('attachable_type', 'Folder')->where('attachable_id', $folderId)->count();
        if($cnt > 0)
            return Responder::json(false)->withMessage('cant_delete_folder')->send();

        $folder = Folder::findOrFail($folderId);
        $folder->delete();

        return Responder::json(true)->send();


    }

    /**
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload($folderId)
    {

        Attachment::processInput('Folder', $folderId);

        return Responder::json(true)->send();
    }
}