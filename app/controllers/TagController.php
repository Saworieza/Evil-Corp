<?php
use \custom\helpers\Midrepo;
use custom\helpers\Responder;

/**
 * Class TagController
 */
class TagController extends BaseController
{

    /**
     * Tag rename
     *
     *
     */
    public function rename($tagId, $newName)
    {
        $tag = Midrepo::get('tag');
        if ($tag->tag !== $newName) {
            $oldName  = $tag->tag;
            $tag->tag = $newName;
            $tag->save();
            foreach ($tag->contents as $content) {
                $content->renameTag($oldName, $newName);
            }

        }

        return Responder::json(true)->withData($tag)->send();
    }

    /**
     * Destroy a tag
     *
     */
    public function destroy()
    {
        $tag     = Midrepo::get('tag');
        $oldName = $tag->tag;
        foreach ($tag->contents as $content) {
            $content->renameTag($oldName);
        }

        $tag->delete();

        return Responder::json(true)->send();

    }

    /**
     * Replace a tag with another
     *
     */
    public function replace()
    {

        $tag     = Midrepo::get('tag');
        $withTag = Midrepo::get('withTag');

        $newName = $withTag->tag;
        foreach ($tag->contents as $content) {
            $content->renameTag($tag->tag, $newName);
            $content->tags()->detach($tag->id);

            if (!$content->tags->contains($withTag->id)) {
                $content->tags()->attach($withTag->id);
            }

        }

        $tag->delete();

        return Responder::json(true)->send();

    }

    /**
     * getList
     *
     */
    public function getList()
    {
        $space = Midrepo::getOrFail('space');

        return Responder::json(true)->withData($space->tags)->send();

    }
}