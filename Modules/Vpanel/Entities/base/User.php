<?php

namespace Modules\Vpanel\Entities\base;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Fields\BoolField;
use Modules\Vpanel\Core\Fields\ImageField;
use Modules\Vpanel\Core\Fields\PasswordField;
use Modules\Vpanel\Core\Fields\StringField;
use Modules\Vpanel\Core\ModelStructure;
use Modules\Vpanel\Database\factories\UserFactory;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasApiTokens;
    use HasFactory;
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'vpanel_users';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function newFactory(): Factory
    {
        return new UserFactory();
    }

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->addField(
                StringField::create()
                    ->setName('name')
                    ->setTitle('Имя')
                    ->identify()
                    ->required()
                    ->showInFilter()
                    ->showInSearch()
            )
            ->addField(
                StringField::create()
                    ->setName('login')
                    ->setTitle('Логин')
                    ->required()
                    ->showInFilter()
                    ->showInSearch()
            )
            ->addField(
                StringField::create()
                    ->setName('email')
                    ->setTitle('Email')
                    ->showInFilter()
                    ->showInSearch()
            )
            ->addField(
                PasswordField::create()
                    ->setName('password')
                    ->setTitle('Пароль')
                    ->hideFromEditor()
                    ->required()
            )
            ->addField(
                ImageField::create()
                    ->setName('avatar')
                    ->setTitle('Аватар')
            )
            ->addField(
                StringField::create()
                    ->setName('role')
                    ->setTitle('Роль')
                    ->hideFromForm()
            )
            ->addField(
                BoolField::create()
                    ->setName('has_access')
                    ->setTitle('Доступ в систему')
            )
            ->setFormComponent('UserModelForm')
            ->setSoftDelete()
            ->setModelTitle('Пользователи')
            ->setRecordTitle('пользователь')
            ->setAccusativeRecordTitle('пользователя');
    }
}
