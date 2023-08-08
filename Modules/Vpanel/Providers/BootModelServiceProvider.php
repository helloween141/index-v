<?php

namespace Modules\Vpanel\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Modules\Vpanel\Core\Utils;
use Nwidart\Modules\Facades\Module;

class BootModelServiceProvider extends ServiceProvider
{
    /**
     * Get the services provided by the provider.
     *
     * @return void
     * @throws \Exception
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }
        
        $modules = Module::allEnabled();
        foreach ($modules as $module) {
            /** @var File $allEntities */
            $allEntities = File::glob($module->getPath() . '/Entities/base/*.php');

            foreach ($allEntities as $entity) {
                $modelClass = Utils::getModelClass($module->getName(), pathinfo($entity, PATHINFO_FILENAME));
                $structure = $modelClass::getStructure();
                if ($structure) {
                    $masterModel = $structure->getMasterModel();
                    if ($masterModel) {
                        ($masterModel->getClass())::getStructure()->addChildModelData([
                            'title' => $structure->getTitle(),
                            'model' => $modelClass,
                            'relation_key' => $masterModel->getRelationKey(),
                            'tab' => $masterModel->isTab()
                        ]);
                    }
                }
            }
        }
    }
}
