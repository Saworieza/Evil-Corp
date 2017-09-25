<?php

namespace custom\helpers;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class FileProcesor {



    public $code;
    protected $newFile;
    protected $moved = false;
    protected $originalName;

    function __construct($file)
    {
        $this->originalName = $file->getClientOriginalName();
        $this->code = md5(date('Ymdhis').rand(100, 999));
        $newFileName = $this->code.'_'.$file->getClientOriginalName();
        $file->move(filesFolder(), $newFileName);
        $this->newFile = $newFileName;

    }

    public function getFullPath()
    {
        return filesFolder().$this->newFile;
    }

    public function getFileName()
    {
        return $this->originalName;
    }

    public function getExtension()
    {
        return strtolower(File::extension($this->getFullPath()));
    }

    public function getSize()
    {
        return File::size($this->getFullPath());
    }

    public function isImage()
    {

        if(extension_loaded('mbstring') && extension_loaded('exif') && extension_loaded('fileinfo'))
           return ( exif_imagetype($this->getFullPath()) > 0 );

        return false;
    }

    public function imageResize($w, $h)
    {
        $img = Image::make($this->getFullPath());
        if($img->width() >= $img->height())
        {
            $img->resize($w, null, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $img->resize(null, $h, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $img->save($this->getFullPath());
    }

    public function genPreview()
    {
        $img = Image::make($this->getFullPath());

        $img->fit(140, 140, function ($constraint) {
            $constraint->upsize();
        });

        $img->resizeCanvas(150,150,'center',false, 'ffffff');

        $img->save(filesFolder().'p_'.$this->newFile);
    }


}