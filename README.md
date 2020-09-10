# Data Locker: библиотека для защиты информации одноразовым паролем

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/badges/build.png?b=master)](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/artem-prozorov/data-locker/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## Установка

`composer require prozorov/data-locker`

## Использование

### Хранилище паролей

Сперва нужно создать адаптер для репозитория одноразовых паролей. Репозиторий - это класс, реализующий интерфейс `Prozorov\DataVerification\Contracts\CodeRepositoryInterface`.

### Конфигурация пакета

Далее нужно настроить класс, содержащий настройки для работы библиотеки `Prozorov\DataVerification\Configuration`. В конструктор этого класса нужно передать контейнер, совместимый с PSR-11 (`Psr\Container\ContainerInterface`) и конфигурационный массив.

Пример конфигурационного массива:
```
$config = [
    'code_repository' => OtpCodeTable::class,
    'pass_length' => 4,
    'creation_code_threshold' => 60,
    'limit_per_hour' => 10,
    'attempts' => 3,
    'password_validation_period' => 3600,
    'transport_config' => [
        'sms' => function () use ($debugCodePath) {
            return new DebugTransport($debugCodePath);
        },
    ],
    'messages' => [
        'sms' => SmsMessage::class,
    ],
];
```

## Параметры конфигурации

### code_repository

Класс-репозиторий для хранения паролей.

Допустимые значения: 
- Строка. В случае, если передана строка, библиотека обратится к PSR-11 контейнеру чтобы получить класс. Класс должен реализовывать интерйейс `Prozorov\DataVerification\Contracts\CodeRepositoryInterface`.
- Замыкание, возвращающее экземпляр класса, реализующего `Prozorov\DataVerification\Contracts\CodeRepositoryInterface`. Замыкание будет выполнено единоразово, а не каждый раз при запросе репозитория.
- Объект, реализующий интерфейс `Prozorov\DataVerification\Contracts\CodeRepositoryInterface`.

Обязательный параметр.
Значение по умолчанию: отсутствует.

### pass_length

Длина пароля. 

Допустимые значения:
- Число больше 0

Необязательный параметр.
Значение по умолчанию: 4.

### creation_code_threshold

Время (количество секунд), в течение которого нельзя запросить код повторно на один и тот же адрес.

Допустимые значения:
- Число больше 0

Необязательный параметр.
Значение по умолчанию: 60.

### limit_per_hour

Количество попыток, которые можно сделать за час на один и тот же адрес

Допустимые значения:
- Число больше 0.

Необязательный параметр.
Значение по умолчанию: 10.

### attempts

Количество попыток ввода пароля.

Допустимые значения:
- Число больше 0

Необязательный параметр.
Значение по умолчанию: 3.

### password_validation_period

Время (количество секунд) жизни пароля.

Допустимые значения:
- Число больше 0.

Необязательный параметр.
Значение по умолчанию: 3600 (1 час).

### transport_config

Настройки системы доставки сообщений

Допустимые значения: массив, ключами которого являются символьные коды метода доставки (например, `sms`, `email`) а значениями - - Объекты, реализующие интерфейс `Prozorov\DataVerification\Contracts\TransportInterface`, 
- Строковые названия классов, рализующих интерфейс `Prozorov\DataVerification\Contracts\TransportInterface`,
- Замыкания, возвращающие объекты, реализующие интерфейс `Prozorov\DataVerification\Contracts\TransportInterface`.

### messages

Настройки сообщений

Допустимые значения: массив, ключами которого являются символьные коды метода доставки (например, `sms`, `email`) а значениями - - Объекты, наследующиеся от `Prozorov\DataVerification\Messages\AbstractMessage`, 
- Строковые названия классов, наследующиеся от `Prozorov\DataVerification\Messages\AbstractMessage`,
- Замыкания, возвращающие объекты, наследующиеся от `Prozorov\DataVerification\Messages\AbstractMessage`.
