<?php

namespace Modules\Archive\Services;

use Illuminate\Http\UploadedFile;
use Modules\Archive\Entities\base\File;
use Modules\Archive\Entities\base\Image;

class UploadService
{
    public function __invoke(UploadedFile $upload, string $type = 'file'): File|Image
    {
        if ($type === 'image') {
            $entity = new Image();
            $entity->path = $upload->store('images', 'public');
        } else {
            $entity = new File();
            $entity->path = $upload->store('files', 'public');
        }
        $entity->name = $upload->getClientOriginalName();
        $entity->save();

        return $entity;
    }
}