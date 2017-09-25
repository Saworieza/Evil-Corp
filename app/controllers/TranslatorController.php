<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;
use custom\helpers\BingTranslator;


class TranslatorController extends BaseController
{

    public function translate($type, $id)
    {
        $toLang = Auth::user()->language;


        if($type == 'Content')
        {
            $cnt = Content::findOrFail($id);


            $txt = $cnt->content_text;

            $trn = Translation::where('translatable_type', $type)->where('translatable_id', $id)->first();

            if(isset($trn))
            {
                $trans = json_decode($trn->translations, true);

                if( isset($trans[$toLang]) )
                {
                    return Responder::json(true)->withData(['translated'=>$trans[$toLang]])->send();
                }
            }


            $BingTranslator = new BingTranslator('linkrapp', '6h0pL681RC3tM9X73+OgMjt8Muu8RyuExqet14DjuGU=');

            $translated = $BingTranslator->getTranslation('en', $toLang, $txt);

            if(! isset($trn))
            {
                $trn = new Translation();
                $trn->translatable_type = 'Content';
                $trn->translatable_id = $id;
                $trn->translations = json_encode([$toLang=>$translated]);
                $trn->save();

            } else {

                $trans[$toLang] = $translated;
                $trn->translations = json_encode($trans);
                $trn->save();
            }

            return Responder::json(true)->withData(['translated'=>$translated])->send();




        }
    }

}