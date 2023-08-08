<?php

namespace Modules\Archive\Entities\base;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Archive\Database\factories\FileFactory;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Fields\StringField;
use Modules\Vpanel\Core\ModelStructure;

class File extends BaseModel
{
    use SoftDeletes;
    use HasFactory;

    public $timestamps = true;

    protected $table = 'archive_files';

    protected static function newFactory(): Factory
    {
        return new FileFactory();
    }

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->addField(
                StringField::create()
                    ->setName('name')
                    ->setTitle('Название')
                    ->identify()
                    ->required()
                    ->showInSearch()
            )
            ->addField(
                StringField::create()
                    ->setName('path')
                    ->setTitle('Путь')
                    ->hideFromForm()
            )
            ->setAlias('file')
            ->setEditorComponent('ArchiveEditor')
            ->canAdd(false)
            ->setSoftDelete()
            ->setModelTitle('Файлы')
            ->setRecordTitle('файл')
            ->setAccusativeRecordTitle('файл');
    }
}
