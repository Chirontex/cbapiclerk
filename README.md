# CBAPIClerk

**CBAPIClerk** — это модуль, позволяющий работать с API CRM-системы «Клиентская база», практически не заботясь о реализации.

[![версия](https://img.shields.io/badge/%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F-1.0.0-blue "версия")](htthttps://img.shields.io/badge/%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F-1.0.0-bluep:// "версия") [![версия PHP](https://img.shields.io/badge/PHP-7.3%2B-blue "версия PHP")](httphttps://img.shields.io/badge/PHP-7.3%2B-blue:// "версия PHP")

## Установка

`composer require infernusophiuchus/cbapiclerk`

## Как пользоваться

Нужно создать объект:

```php
<?php
use Infernusophiuchus\CBAPIClerk\Handle as CBAPIClerk;
use Infernusophiuchus\CBAPIClerk\Exceptions\HandleException;

try {

	$cbapiclerk = new CBAPIClerk(
		'https://your-site.ru/', // адрес вашей «Клиентской базы»
		'login', // логин пользователя с включенным доступом по API
		'apikey' // ключ, сгенерированный системой
	);

} catch (HandleException $e) {}

```

Работа с API «Клиентской базы» производится посредством вызова определённых методов объекта.

### Методы и API-маршруты

_/api/data/create_ — **dataCrud**('create', $command), **dataCreate**($command) \n
_/api/data/read_ — **dataCrud**('read', $command), **dataRead**($command)
_/api/data/update_ — **dataCrud**('update', $command), **dataUpdate**($command)
_/api/data/delete_ — **dataCrud**('delete', $command), **dataUpdate**($command)
_/api/group/get_list_ — **getList**('group'), **groupList**()
_/api/table/get_list_ — **getList**('table'), **tableList**()
_/api/table/get_perms_ — **tableDetails**('perms', $id), **tablePerms**($id)
_/api/table/info_ — **tableDetails**('info', $id), **tableInfo**($id)
_/api/user/get_list_ — **getList**('user'), **userList**(),
_/api/data/files_ — **files**($command)

P.S.:
$command — массив с параметрами запроса (см. https://clientbase.ru/help/for_admin_16/api/)
$id — ID сущности в системе, тип **int**
