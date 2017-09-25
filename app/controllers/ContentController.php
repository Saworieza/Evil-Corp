<?php

use custom\helpers\Midrepo;
use custom\exceptions\ApiException;
use custom\helpers\Notificator;
use custom\helpers\Responder;
use custom\helpers\UrlInfo;

/**
 * Class ContentController
 */
class ContentController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $content = Content::stream()->with('user')->with('sharedFrom')
            ->fromSpace(Midrepo::getOrFail('space')->id);

        if (Input::has('class_id'))
            $content = $content->where('class_id', Input::get('class_id'));


        if (Input::has('starred')) {
            $content = $content->whereHas(
                'starred', function ($q) {
                    $q->where('user_id', Auth::user()->id);
                }
            );
        }

        if (Input::has('tag')) {
            $content = $content->whereHas(
                'tags', function ($q) {
                    $q->where('tag_id', Input::get('tag'));
                }
            );
        }

        if (Input::has('from_date')) {

            $content = $content->where('updated_at', '<', Input::get('from_date'));
        }

        $content = $content
            ->take(10)
            ->orderBy('updated_at', 'DESC')
            ->get();


        return Responder::json(true)->withDataTransform($content, 'ContentTransformer')->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $content = Midrepo::getOrFail('content');
        $content->delete();

        $tags = [];
        if ($content->tagsChanged)
            $tags = Space::findOrFail($content->space_id)->tags()->orderBy('counter', 'DESC')->get()->toArray();

        return Responder::json(true)->withData($tags)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaticMap()
    {
        if (!Input::has('address')) Responder::json(false)->send();

        $zoom    = Input::get('zoom', 14);
        $address = Input::get('address');

        $target = "http://maps.google.com/maps/api/staticmap?center={$address}&zoom={$zoom}&size=600x400&sensor=false&markers={$address}";

        return Responder::json(true)->withData(['image' => $target])->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUrl()
    {
        $url = UrlInfo::parse(Input::get('url'));

        if (!$url) {
            return Responder::json(false)->withMessage('url_not_found')->send();
        }

        return Responder::json(true)->withData($url->toArray())->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewMore()
    {
        $content = Midrepo::getOrFail('content');
        return Responder::json(true)->withData($content->content_text)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVoteOption()
    {
        $content = Midrepo::getOrFail('content');
        if ($content->class_id != CONTENT_POLL) return Responder::json(false)->send();

        $data                  = $content->content_data;
        $data['options'][]     = Input::get('option');
        $content->content_data = json_encode($data);
        $content->save();

        $content->touch();

        $contentVotes = Content::where('id', $content->id)
            ->with('votes')
            ->with('votes.user')
            ->first();

        return Responder::json(true)->withData($data)->send();
    }

    /**
     * User vote in a poll
     *
     * @param $contentId
     * @param $optionIndex
     * @return \Illuminate\Http\JsonResponse
     */
    public function vote($contentId, $optionIndex)
    {
        $content = Midrepo::getOrFail('content');
        if ($content->class_id != CONTENT_POLL) return Responder::json(false)->send();

        $vote = Vote::where('content_id', $contentId)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!isset($vote)) {
            $vote = Vote::create(
                [
                    'user_id'    => Auth::user()->id,
                    'content_id' => $contentId,
                    'choice'     => $optionIndex
                ]
            );
        } else {
            $vote->choice = $optionIndex;
            $vote->save();
        }

        $contentVotes = Content::where('id', $contentId)
            ->with('votes')
            ->with('votes.user')
            ->first();

        $content->updated_by = Auth::user()->id;
        $content->save();

        return Responder::json(true)->withData($contentVotes)->send();

    }

    /**
     * Event assist to event
     *
     * @param $contentId
     * @param $assistValue
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventAssist($contentId, $assistValue)
    {
        $content = Midrepo::getOrFail('content');

        if ($content->class_id != CONTENT_EVENT) return Responder::json(false)->send();

        Attendant::upsert($content->calendar->id, $assistValue);

        $cal = Calendar::find($content->calendar->id);

        $cal->load('attendants.user');

        return Responder::json(true)->withData($cal)->send();
    }

    /**
     * @param $spaceCode
     * @param $maxId
     * @return \Illuminate\Http\JsonResponse
     */
    public function pool($spaceCode, $maxId)
    {

        $count = DB::table('content')
            ->where('space_id', Midrepo::getOrFail('space')->id)
            ->where('updated_at', '>', gmdate("Y-m-d H:i:s", $maxId))
            ->where('updated_by', '<>', Auth::user()->id)
            ->count();

        return Responder::json(true)->withData($count)->send();
    }

    /**
     * Store content
     * Route: post:/content
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function store()
    {
        if (!$className = Midrepo::getOrFail('space')->validateClass())
            throw new ApiException('not_found');

        $content = new $className;

        $content->createFromInput();

        if (!$content->save())
            throw new ApiException($content->validator);

        Attachment::processInput('Content', $content->id);

        $content = $this->one($content->id);

        Notificator::newPost($content);

        return Responder::json(true)->withDataTransform($content, 'ContentTransformer')->send();

    }



    private function one($contentId)
    {
        $content = Content::stream()->with('space')->with(
            array(
                'user' => function ($query) {
                    $query->select('id', 'full_name');
                }
            )
        )->findOrFail($contentId);

        return $content;
    }

    /**
     * @param $contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne($contentId)
    {
        return Responder::json(true)->withDataTransform($this->one($contentId), 'ContentTransformer')->send();
    }


    public function csv()
    {
        $content = Midrepo::getOrFail('content');
        $data    = $content->content_data['rows'];
        $cols    = array_pluck($content->content_data['cols'],'title');

        exportCSV($cols, $data);

    }

    public function share($contentId, $code)
    {
        $from = Midrepo::getOrFail('content');
        $space = Midrepo::getOrFail('space');

        $to = new Content();
        $to->space_id = $space->id;
        $to->user_id = Auth::user()->id;
        $to->content_text = $from->content_text;
        $to->class_id = $from->class_id;
        $to->content_data = json_encode($from->content_data);
        $to->shared_from_id = $from->user_id;
        $to->save();

        //copy attachments
        foreach($from->attachments as $att)
        {
            $att->replicate();
            $att->attachable_id = $to->id;
            $att->save();
        }



        return Responder::json(true)->withData($from)->send();
    }
}
