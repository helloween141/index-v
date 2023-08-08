# index.CMS

## Установка (без Docker):

1. ```php composer install --prefer-dist```

2. Создать файл .env (см .env.example) и задать APP_NAME, APP_URL

3. ```php artisan module:migrate --seed```

4. ```npm i```

5. ```npm run dev```

## Установка (Docker):

1. Запустить
   команду: ```docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -v ~/.ssh:/home/index/.ssh -w /var/www/html $(docker build --no-cache --build-arg uid=$(id -u) --build-arg gid=$(id -g) -q .) /usr/bin/composer install --ignore-platform-reqs```

2. Добавить алиас: ```alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'```

3. Поднять контейнеры в фоновом режиме: ```./vendor/bin/sail up -d```

4. Создать файл .env (см .env.example) и задать APP_NAME, APP_URL

5. ```sail npm i```

6. ```sail npm run dev```

## Доступы для входа в панель администратора по умолчанию

> admin / admin

## Часто используемые консольные команды

````
# Создать модуль
php artisan module:make <module_name>

# Создать модели
php artisan module:make-base-model <model_name> <module_name>
php artisan module:make-model <model_name> <module_name>

# Создать миграцию для модуля
php artisan module:make-migration <migration_name> <module_name>

# Применить миграции в конкретном модуле. Если не указать <module_name>, то миграции применятся во всех модулях
php artisan module:migrate <?module_name>

# Очистить данные в БД и применить миграции заново
php artisan migrate:fresh

# Применить миграции заново и заполнить данными
php artisan module:migrate-refresh --seed

# Обновить документацию для API
php artisan scribe:generate

# Полная пересборка прав
php artisan vpanel:rebuild-permissions

# Полная пересборка подчинений
php artisan vpanel:rebuild-subordinates

# Установить симлинк для папки public (открывает доступ извне к статике)
php artisan storage:link

# Вывести список задач по расписанию
php artisan schedule:list

# Запуск планировщика
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
````

## Ссылки

> Документация по модулям: https://docs.laravelmodules.com/v10/introduction

> Документация по шаблонизатору Mustache: https://github.com/janl/mustache.js

> Документация по Scribe (автоматическая генерация описания API): https://scribe.knuckles.wtf/laravel/

## Документация

Используемый стек: Laravel 9+, PHP 8.0+, Vue3, PostgreSQL 15.0+ / MySQL 8.0+

В общем случае, разработка выглядит так: создается модуль, затем миграции, после чего создаются модели
в папке Entities. В базовой модели необходимо описать структуру.

**Пример создания модуля:**

````
php artisan module:make Blog
php artisan module:make-migration create_blog_table Blog
php artisan module:make-base-model Blog Blog
php artisan module:make-model Blog Blog
````

### Конфигурация модуля

Каждый модуль содержит файл module.json, в котором описывается конфигурация модуля для фронтенда. В нем
так же можно определить вывод ссылок на модели в боковом меню (sidebar).

**Пример:**

````json
{
  "name": "Vpanel",
  "menu": {
    "title": "Система",
    "icon": "fa-solid fa-key",
    "list": [
      {
        "model": "User",
        "title": "Пользователи"
      },
      {
        "model": "Role",
        "title": "Роли"
      }
    ]
  },
  "alias": "vpanel",
  "description": "",
  "keywords": [],
  "priority": 0,
  "providers": [
    "Modules\\Vpanel\\Providers\\VpanelServiceProvider",
    "Modules\\Vpanel\\Providers\\BootModelServiceProvider"
  ],
  "aliases": {},
  "files": [],
  "requires": []
}
````

### Структура модели

Для определения структуры модели необходимо:

1. Создать модель, которая наследуется от базового класса
   BaseModel. Модели располагаются в папке Modules/<название модуля>/Entities.

2. Переопределить метод defineStructure() базового класса BaseModel, который создает структуру.

Описание структуры осуществляется посредством вызовов методов по цепочке (chaining).<br />

**В простейшем случае структура модели имеет вид:** <br />

````php 
class MyClass extends BaseModel
{
   public static function defineStructure(): ModelStructure {
      return ModelStructure::create();
   }
}
````

### Методы структуры модели

> ```setModelTitle(string $value)``` - задать название модели

> ```setRecordTitle(string $value)``` - задать название детализации (в им. падеже)

> ```setAccusativeRecordTitle(string $value)``` - задать название детализации (в род. падеже)

> ```setSortable()``` - задать признак сортировки записей в табличном представлении.
> (дополнительно необходимо создать в БД поле sort с типом int)

> ```setRecursive(bool $expand = false)``` - задать признак рекурсивной модели. Если передать параметр $expand = true,
> то будет отображаться раскрытая древовидная структура.
> (!) Так же необходимо у поля, которое будет являться внешним ключом вызывать метод parent()

> ```disableRightsControl()``` - отключить контроль по правам

> ```canAdd(bool $value)``` - возможность создания записи

> ```canEdit(bool $value)``` - возможность сохранения записи

> ```canDelete(bool $value)``` - возможность удаления записи

> ```setAlias()``` - задать алиас модели (используется для вывода названия модели в представление)

> ```addField(Field $field)``` - создать поле определенного типа. См. "Методы структуры поля"

> ```setSingle()``` - задать признак модели с одной записью

> ```addChildModel(BaseModel $model)``` - добавить связь с подчиненной моделью. См "Методы структуры подчиненной модели"

> ```addEditorGroup(BaseModel $model)``` - добавить группу для полей. См "Методы структуры группы"

> ```setEditorComponent($string value)``` - указать кастомный компонент для табличного представления.
> Компонент необходимо расположить в папке: Modules/<module_name>/Resources/scripts/components/editors/*.vue

> ```setFormComponent($string value)``` - указать кастомный компонент для детализации.
> Компонент необходимо расположить в папке: Modules/<module_name>/Resources/scripts/components/forms/

> ```setSoftDelete()``` - задать признак "мягкого удаления" (запись не удаляется окончательно).
> Дополнительно необходимо подключить трейт ```use SoftDeletes``` в базовой модели и создать поле deleted_at с типом
> timestamp

> ```setSortByField(string $field, string $order = 'desc')``` - установить поле для сортировки


### Типы полей

| Тип           | Описание                                          |
|---------------|---------------------------------------------------|
| IntField      | целое число                                       
| FloatField    | число с плавающей точкой                          |
| BoolField     | флаг (булевский тип)                              |
| StringField   | строка                                            |
| TextField     | многострочный текст                               |
| HtmlField     | текстовое поле с инструментами для редактирования |
| DateField     | дата/время                                        |
| FileField     | файл                                              |
| ImageField    | изображение                                       |
| PasswordField | пароль                                            |
| PointerField  | указатель на модель                               |
| SelectField   | выбор из списка значений                          |

**Пример:** `->addField(IntField::create())`<br />

### Методы структуры поля <br />

> ```setName(string $value)``` - задать имя поля

> ```setTitle(string $value)``` - задать название поля

> ```identify()``` - идентифицировать поле (по этому полю выводится список значений в PointerField)

> ```required(array $dependencies = [])``` - поле обязательное для заполнения.
> В качестве необязательного параметра можно передать массив зависимостей. В основном используется в связке с
> showCondition. Например (dependencies: ['type' => 'some-value']). В этом случае поле будет обязательным для
> заполнения, если значение поля с названием 'type' === 'some-value'

> ```readonly()``` - поле доступное только для чтения

> ```showInFilter(array $filterConfig = [])``` - отображать поле в фильтре. В качестве параметра можно передать
> конфигурацию. <br />
> Доступные опции:
> ```` 
>  [ 
>    multiple_selection => true // множественный выбор (по умолчанию true) 
>  ]
> ````

> ```showInSearch()``` - отображать поле в поиске

> ```hideFromEditor()``` - скрыть поле в табличном представлении

> ```hideFromForm()``` - скрыть поле в детализации

> ```setDefaultValue(mixed $value)``` - установить значение по умолчанию

> ```setTooltip(string $value)``` - установить подсказку

> ```setGroup(string $value)``` - задать название группы (необходимо описать addEditorGroup() в структуре модели для
> группировки полей)

> ```setOptions(array $options)``` - задать список значений (только для SelectField)

> ```modal()``` - отображать в модальном окне (только для PointerField)

> ```parent()``` - сделать поле родительским (необходимо вызвать setRecursive() в структуре модели). Поле будет являться
> внешним ключом к текущей таблице.

> ```calc(<sql_expression>)``` - вычисляемое значение для поля. В качестве аргумента передается SQL-выражение в
> зависимости от диалекта

> ```showCondition(<javascript_expression>)``` - условие отображения в детализации в зависимости от значения других
> полей.
> В качестве аргумента передается JavaScript-выражение. Обратиться к значениям текущей записи можно через
> переменную record. <br />
> Например: ('record.type === "some_type"'). Т.е, поле, у которого указан showCondition
> будет отображаться, если значение другого поля type строго совпадает со строкой "some_type"

> ```filterCondition(<sql_expression>)``` - условие фильтрации для PointerField в зависимости от значения других полей.
> В качестве аргумента передается SQL-выражение. Обратиться к значениям текущей записи можно через
> *<variable_name>* <br />
> Например: (" name like '%*title*%' "), в случае с переменной или (" name like '%Тестовое имя%' "), где name -
> поле модели, на которую ссылается PointerField

### Методы структуры подчиненной модели

Для объявления признака того, что модель является подчиненной необходимо вызвать метод setMasterModel(),
Подчиненная модель может отображаться как в виде вкладки (tab), так и ниже детализации мастер-формы.

**Пример:** `->setMasterModel(MasterModel::create())`<br />

> ```setModel(BaseModel $model)``` - указать мастер-модель (путь к классу модели)

> ```setRelationKey(string $value)``` - задать имя внешнего ключа (по нему подчиненная модель связывается с
> мастер-моделью)

> ```showAsTab()``` - отображать в виде вкладки. По умолчанию отображается в виде встроенной таблицы

### Методы структуры группы

Группы используются для объединения в табличном представлении. Шаблон описывается при помощи
шаблонизатора Mustache.

**Пример:** `->addEditorGroup(Group::create())`<br />

> ```setName(string $value)``` - задать имя группы (по этому имени группируются поля setGroup())

> ```setTitle(string $value)``` - задать название группы (отображается в колонке в табличном представлении)

> ```setTemplate(string $value)``` - задать шаблон (в качестве значения передается шаблон Mustache.
> Внутри него есть доступ к переменным, которые относятся к данной группе)

### Создание виджетов для ролей

Виджеты - это компоненты, которые привязаны к определенным ролям и отображаются на дашборде. Каждому модулю
может соответствовать свой набор виджетов.

Для создания виджета необходимо:

1. Создать компонент и разместить в папке widgets по следующему пути: Modules/<Название модуля>
   /Resources/scripts/components/widgets/
2. Определить в файле module.json нужного модуля свойство widgets (если оно не создано) и описать виджет:

**Пример:**

````json
{
   "widgets": [
      {
         "title": "Мой виджет 1",
         "component": "CustomWidget"
      },
      {
         "title": "Мой виджет 2",
         "component": "CustomWidget2"
      }
   ]
}
````

После чего перейти в интерфейс редактирования роли, затем во вкладку "Виджеты" и выбрать нужные виджеты для
отображения

### Пример полного описания структуры модели

````php
public static function defineStructure(): ModelStructure
{
  return ModelStructure::create()
      ->addField(
          StringField::create()
              ->setName('title')
              ->setTitle('Название')
              ->identify()
              ->required()
              ->showInFilter()
              ->showInSearch()
              ->setGroup('main_info')
      )
      ->addField(
          BoolField::create()
              ->setName('show')
              ->setTitle('Активно')
              ->setTooltip('Показывать на сайте')
              ->showInFilter()
              ->hideFromEditor()
      )
      ->addField(
          DateField::create()
              ->setName('date')
              ->setTitle('Дата')
              ->setDefaultValue(date('Y-m-d'))
              ->showInFilter()
      )
      ->addField(
          IntField::create()
              ->setName('price')
              ->setTitle('Стоимость')
              ->setTooltip('В рублях')
              ->showInFilter()
              ->showCondition('record.show && record.author_id === 2')
              ->setGroup('main_info')
      )
      ->addField(
          SelectField::create()
              ->setName('type')
              ->setTitle('Тип')
              ->setOptions([
                  'type1' => 'Тип 1',
                  'type2' => 'Тип 2',
                  'type3' => 'Тип 3'
              ])
              ->showInFilter()
              ->setGroup('additional_info')
      )
      ->addField(
          PointerField::create()
              ->setName('author')
              ->setTitle('Автор')
              ->required()
              ->setModel(Author::class)
              ->filterCondition("name like '%*title*%'")
              ->modal()
              ->showInFilter()
              ->showInSearch()
              ->setGroup('additional_info')
      )
      ->addField(
          FileField::create()
              ->setName('file')
              ->setTitle('Файл')
              ->hideFromEditor()
      )
      ->addField(
          ImageField::create()
              ->setName('image')
              ->setTitle('Изображение')
      )
      ->addField(
          TextField::create()
              ->setName('short_text')
              ->setTitle('Краткое описание')
      )
      ->addField(
          HtmlField::create()
              ->setName('full_text')
              ->setTitle('Полное описание')
              ->hideFromEditor()
      )
      ->addField(
          PointerField::create()
              ->setName('parent')
              ->setTitle('Родительская новость')
              ->setModel(\Modules\Blog\Entities\News::class)
              ->hideFromEditor()
              ->parent()
      )
      ->addEditorGroup(
          Group::create()
              ->setName('main_info')
              ->setTitle('Основная информация')
              ->setTemplate(
                  '
                  <span>{{#title}}Заголовок:{{title}}{{/title}}</span>
                  <span>{{#price}}Цена:{{price}}{{/price}}</span>
                  '
              )
      )
      ->addEditorGroup(
          Group::create()
              ->setName('additional_info')
              ->setTitle('Доп информация')
              ->setTemplate(
                  '
                  <span>{{#type}}Тип:{{type}}{{/type}}</span>
                  <span>{{#author_id}}Автор:{{author_id}}{{/author_id}}</span>
                  '
              )
      )
      ->setSortable()
      ->setRecursive()
      ->setModelTitle('Новости')
      ->setRecordTitle('новость')
      ->setAccusativeRecordTitle('новость');
}
````
