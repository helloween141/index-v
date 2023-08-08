<?php

namespace Modules\Vpanel\Core;

use Modules\Vpanel\Core\Fields\Field;
use Modules\Vpanel\Core\Fields\StringField;
use Modules\Vpanel\Core\Fields\TextField;

class ModelStructure
{
    protected string $alias = '';

    protected array $fields = [];

    protected array $editorGroup = [];

    protected string $title = '';

    protected string $recordTitle = '';

    protected string $accusativeRecordTitle = '';

    protected string $editorComponent = '';

    protected string $formComponent = '';

    protected string $sortField = '';

    protected string $sortOrder = '';

    protected bool $sortable = false;

    protected bool $recursive = false;

    protected bool $expandTree = false;

    protected bool $single = false;

    protected bool $showBackButton = true;

    protected bool $showCreateButton = true;

    protected bool $showDeleteButton = true;

    protected bool $showSaveButton = true;

    protected bool $rightsControl = true;

    protected ?MasterModel $masterModel = null;

    protected array $childModels = [];

    protected bool $softDelete = false;

    public static function create(): ModelStructure
    {
        return new ModelStructure();
    }

    public function addField(Field $field): ModelStructure
    {
        $this->fields[] = $field;
        return $this;
    }

    public function setModelTitle(string $title): ModelStructure
    {
        $this->title = $title;
        return $this;
    }

    public function setAccusativeRecordTitle(string $title): ModelStructure
    {
        $this->accusativeRecordTitle = $title;
        return $this;
    }

    public function setRecordTitle(string $title): ModelStructure
    {
        $this->recordTitle = $title;
        return $this;
    }

    public function addEditorGroup(Group $group): ModelStructure
    {
        $this->editorGroup[] = $group;
        return $this;
    }

    /*
     * Используется как имя модели в нижнем регистре
     */
    public function setAlias(string $alias): ModelStructure
    {
        $this->alias = $alias;
        return $this;
    }

    public function setEditorComponent(string $name): ModelStructure
    {
        $this->editorComponent = $name;
        return $this;
    }

    public function setFormComponent($name): ModelStructure
    {
        $this->formComponent = $name;
        return $this;
    }

    public function setSortable(): ModelStructure
    {
        $this->sortable = true;
        return $this;
    }

    public function setRecursive(bool $expand = false): ModelStructure
    {
        $this->expandTree = $expand;
        $this->recursive = true;
        return $this;
    }

    public function setSingle(): ModelStructure
    {
        $this->single = true;
        return $this;
    }

    public function setSoftDelete(): ModelStructure
    {
        $this->softDelete = true;
        return $this;
    }

    public function getMasterModel(): ?MasterModel
    {
        return $this->masterModel;
    }

    public function setMasterModel(MasterModel $masterModel): ModelStructure
    {
        $this->masterModel = $masterModel;
        return $this;
    }

    public function setSortByField(string $field, string $order = 'desc'): ModelStructure {
        $this->sortField = $field;
        $this->sortOrder = $order;
        return $this;
    }

    public function disableRightsControl(): ModelStructure
    {
        $this->rights = false;
        return $this;
    }

    public function hideBackButton(): ModelStructure
    {
        $this->showBackButton = false;
        return $this;
    }

    public function canAdd(bool $value = true): ModelStructure
    {
        $this->showCreateButton = $value;
        return $this;
    }

    public function canEdit(bool $value = true): ModelStructure
    {
        $this->showCreateButton = $value;
        $this->showSaveButton = $value;
        return $this;
    }

    public function canDelete(bool $value = true): ModelStructure
    {
        $this->showDeleteButton = $value;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRecordTitle(): string
    {
        return $this->recordTitle;
    }

    public function getAccusativeRecordTitle(): string
    {
        return $this->accusativeRecordTitle;
    }

    public function getEditorComponent(): string
    {
        return $this->editorComponent;
    }

    public function getFormComponent(): string
    {
        return $this->formComponent;
    }

    public function getChildrenModels(): array
    {
        return [];
    }

    public function getEditorGroup(): array
    {
        return $this->editorGroup;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function getSortDirection(): string
    {
        return $this->sortOrder;
    }

    public function getIdentifyField(): string
    {
        foreach ($this->getFields() as $field) {
            if ($field->isIdentify()) {
                return $field->getName();
            }
        }
        return '';
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

    public function isSingle(): bool
    {
        return $this->single;
    }

    public function isRightsControl(): bool
    {
        return $this->rightsControl;
    }

    public function isSoftDelete(): bool
    {
        return $this->softDelete;
    }

    public function addUrl(): ModelStructure
    {
        return $this->addField(StringField::create()->setName('url')->setTitle('URL')->hideFromEditor());
    }

    public function addMeta(): ModelStructure
    {
        return $this->addField(StringField::create()->setName('meta_title')->setTitle('Meta title')->hideFromEditor())
            ->addField(
                TextField::create()->setName('meta_description')->setTitle('Meta description')->hideFromEditor()
            );
    }

    public function addChildModelData(array $modelData): ModelStructure
    {
        $this->childModels[] = $modelData;
        return $this;
    }
}

