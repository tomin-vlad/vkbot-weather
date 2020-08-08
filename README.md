# VK Weather Bot
Бот для социальной сети [ВКонтакте](https://vk.com). Присылает текущий прогноз погоды для выбранного места в ответ на команду или прикрепленную карту. Работает в том числе в беседах.

## 1. Как работает бот?
Бот присылает текущую погоду в ответ на команду или прикрепленную к сообщению карту с местоположением (недоступно для бесед).

<img src="/public/img/command.jpg" width="250" alt="command"> <img src="/public/img/geo.jpg" width="250" alt="geo">

#### 1.1. Получение погоды по команде
**Руководство:**
1. Откройте диалог с сообществом (личные сообщения), либо добавьте бота в беседу.

2. Напишите команду "`погода`" с названием нужного города в формате "`погода Название_города`". Пример: `погода Санкт-Петербург`. Обратите внимание! Если необходимо узнать у бота погоду в беседе - используйте упоминание перед командой. Пример: `@botpogodka погода Санкт-Петербург`.

_Примечание: регистр написания команды и города значения не имеет._
3. Отправьте команду.

_Примечание: к самому сообщению ничего прикреплять не нужно - бот проигнорирует прикрепленные файлы и попытается распознать текст сообщения. Пересланные сообщения бот так же игнорирует._
4. Подождите - бот пришлет в ответ текущую погоду для данного места.

**Пример запроса:**

    погода Самара

**Пример ответа:**

    Место: Самара (RU)
    Погода: мокрый снег
    Температура: 17°C
    По ощущениям: 15°C
    Влажность: 88 %
    Давление: 763 мм рт. ст.
    Видимость: 10 км
    Ветер: северо-западный, 4 м/c
    Восход: 03:15
    Закат: 20:01
    Обновление: 06.06.2020 20:27

#### 1.2. Получение погоды по местоположению
**Руководство**
1. Откройте диалог с сообществом (личные сообщения). Обратите внимание! В режиме бесед данный функционал недоступен.

2. Прикрепите к сообщению карту с местоположением.

3. В тексте сообщения ничего не указывайте. Отправьте прикрепленную карту боту.

_Примечание: иначе - бот попытается распознать текст сообщения, проигнорировав карту._

4. Подождите - бот пришлет в ответ текущую погоду для данного места.

**Пример запроса:**

<img src="/public/img/map.jpg" width="250" alt="map">

**Пример ответа:**

    Место: Петра-Дубрава (RU)
    Погода: гроза
    Температура: 17°C
    По ощущениям: 15°C
    Влажность: 82 %
    Давление: 763 мм рт. ст.
    Видимость: 10 км
    Ветер: северо-западный, 4 м/c
    Восход: 03:13
    Закат: 20:01
    Обновление: 06.06.2020 20:29

## 2. Используемые технологии
Бот написан на [PHP 7.4](https://www.php.net) и использует фреймворк [Lumen](https://lumen.laravel.com) от разработчиков [Laravel](https://laravel.com).

Функционирует на основе: [API VK ботов](https://vk.com/dev/bots_docs), [API OpenWeatherMap](http://openweathermap.org/api).

## 3. Системные требования
* PHP >= 7.2 (с установленными расширениями: OpenSSL, PDO, Mbstring).
* Composer >= 1.8.6
* Git >= 2.22.0
* Домен с подключеным SSL (возможен и бесплатный, от [Let's Encrypt](https://letsencrypt.org/)) - _желательно_.

## 3. Установка бота
1. Склонируйте репозиторий директорию WWW домена, на котором будет работать бот.

2. Настройте рабочим каталогом домена директорию `public` в корне сайта (**только** к этой директории и ее содержимому должен быть публичный доступ). Например, это можно сделать, создав в корне сайта файл `.htaccess` со следующим содержимым:

```
    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
    </IfModule>
```

3. В корне каталога сайта установите PHP-пакеты с помощью менеджера пакетов Composer, выполнив в консоли команду:


    composer i

_Примечание:_ данная команда должна выполняться Composer'ом под версией PHP не ниже, чем указана в системных требованиях.

4. Скопируйте файл `.env.example`, находящийся в корне проекта и переименуйте его в `.env`. Это основной файл настроек приложения.

5. На этом установка завершена. Теперь нужно получить необходимые доступы и настроить бота.

## 4. Настройка бота

1. Перейдите в сообщество VK и получите API-ключ с правом "`Разрешить приложению доступ к сообщениям сообщества`". Подробнее: [https://vk.com/dev/access_token](https://vk.com/dev/access_token). Скопируйте и сохраните ключ, он понадобится далее.

6. Перейдите на вкладку "`Callback API`" в сообществе. Добавьте новый сервер, указав необходимые данные. Подробнее: [https://vk.com/dev/callback_api](https://vk.com/dev/callback_api). Скопируйте и сохраните эти данные, они понадобятся далее.

7. Перейдите на вкладку "`Типы событий`" в сообществе. Отметьте пункт "`Входящее сообщение`" в разделе "`Сообщения`".

8. Зарегистрируйтесь на [OpenWeatherMap](https://home.openweathermap.org/users/sign_up) и получите `APIKEY`. _Примечание:_ Обратите внимание на ограничения бесплатного тарифа: [https://openweathermap.org/price](https://openweathermap.org/price).

9. Вернитесь к файлу `.env` и заполните его полученными на предыдущих шагах данными. Ниже приводится описание параметров:


    "APP_NAME" - имя приложения
    "APP_ENV" - используемое окружение (local - локально, production - боевой режим)
    "APP_KEY" - ключ шифрования (генерируется автоматически)
    "APP_DEBUG" - режим отладки (true - вкл. / false - выкл.)
    "APP_URL" - URL-адрес бота
    "APP_LOCALE" - язык бота (ru / en, можно добавить свой)

    "VK_API_ACCESS_TOKEN" - токен доступа сообщества VK
    "VK_API_CONFIRMATION_TOKEN" - строка, которую должен вернуть сервер
    "VK_GROUP_ID" - id сообщества
    "VK_SECRET_KEY" - секретный ключ сообщества
    "VK_API_VERSION" - используемая версия API VK (должна совпадать с настройками в сообществе)

    "OPENWEATHERMAP_API_URL" - URL-адрес API OpenWeatherMap
    "OPENWEATHERMAP_API_VERSION" - версия API OpenWeatherMap
    "OPENWEATHERMAP_API_ENDPOINT" - эндпоинт API OpenWeatherMap
    "OPENWEATHERMAP_API_ACCESS_TOKEN" - ключ API OpenWeatherMap

10. На этом настройка закончена. Перейдите в сообщество, подтвердите адрес сервера в разделе `Callback API`, вкладка `Настройки сервера` и можете начинать использование бота.

_Примечание:_ Если что-то не пошло не так и бот не реагирует на команды - изучите логи доступа в `storage/logs`, а также проверьте запросы и ответы в сообществе в разделе `Callback API -> Запросы`. Часто это позволяет решить проблему. Кроме того, не забудьте проверить настройки в файле `.env` и ключи доступа (они должны быть актуальны и иметь необходимые права).

## 5. Лицензия
Исходный код данного проекта является открытым и распространяется свободно по лицензии [Creative Commons - Attribution-NonCommercial-NoDerivatives 4.0 International](license.md) (использование в некоммерческих целях без создания производных).