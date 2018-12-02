<?php

return [
    'alipay' => [
        'app_id' => '2016092300580730',
        'ali_public_key' =>
            'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuCOOmF8F7TQgWx506lB5hjv36EBFI1hQS9rOevqX8Sz5+xsHgEeIyEl4BrsUd0uiR15yjOf+ruoXAnvHYu0kzqv5h7dNeio6S7NuqG3A9DoDCFiVzVTNlHfQEpZKZaLqTE08YogYIyOMkthaY3BViGKafzpe683cXdxn2FTTURgDQvqDWup28KZHtbIkw0Cw+RbKRVwHe0M9h9G8+0WzX/sd+qncyS6z7PvAJMI4MM5aFB2co2Otr0wRsmIESA6DSFse1LXVxPa/Kq3jzULa3O366eGcEDRmtnCJEDLRIJgI/HWVOlRsgFTqeHfix4pEPtSaeoyjIhx1sguHxyQSowIDAQAB',
        'private_key' =>
            'MIIEpAIBAAKCAQEA2PTYUjF8BXt7PgMW7KokCS4pnvPtGj/jvP3kOqTts662hdK87BI5/vUld9m+4zIMOxD+xnuhHs1AnX0HafaKef3oUboh0TFg/hDp95PJvMpIox7r9hvZCFbRFGwTuJeN3IK2kCG9FryNa5EMOfaRCnjAGFDsw37MCSfA+aIEj+kKJyb3kIieybGMiZ/FTS6/aQ4yw2c1X+AVWZ/QjIrcxPBfLY6jvq86NTTMlgnkq/SrSuXwplDIzG3sUJWednAgwFyErUfCWl4iR0aPTgEpHaSfP2EcP6T5iaOE4pL1NdDw7b0aL/qj8n1oYQG9snWKHeJ3ZSm/WgfE48JPnFTl2QIDAQABAoIBAE5OIP2Ziq/X36WEK/QDfLorhS4v0DYXNTVzVbqs6HZf6tTmOPXjpjcVeA6H8tPBrpRrD+nHmWGqp1WwWMqwB078i6tjyOzeK6nHpQqMttWDFTB66qOLbYwK+a+rSnrniHY8X3QqGTtplolqcsVA7xyedIbSPoJY83+ib44qIgdmZMw0OOQMMWpclXui2kzEipt6P8/shnD/+ijjaTjjKRzeJuijGTE6v21Glu3TltiDmSyFl42Cu1/whrzBFBl5XpOD0Prp/12cuFlb5haMuEKydQ8a7Jt5Cf+tv2zn3qAQSNT4tZBhllTSaypBKvHGL6YOKHWFVVjjnO2LakbsrQECgYEA9sjdZlE7LxT3mqy2UHT0OSDQgSYgh1x4hcgCGIzAawvYic6sSAH+kjSB6x/FneYQj8FTf0BPWU/lVzJVwu7RqLmu+6nErT/d1PmReWvAFFGzPa+nPmFw2RF/K6f/j+fCbQda26kA4bGm1sk4VXbyU92qUfFJF2bul7oKL7sJOykCgYEA4Q7Wd40gqGkvF188NRpjs2mha/qxpbTkZhvIgq7w7gKyBBgwt2xi5h/u0IfhZJ7yOUbf0wzuh4yCRr986PGk1QFOOadg8dNlKLjT5JIdP9WAnHwjHBKRUOHcXZ2h99hLwxG5CVUtw9Q2pxnkwQL79MVcuLAj/EcSclnKONVhWzECgYBRNlLdu/etC8pb5WBt478HGcxj4+cqhHzJEcPWzmL4F0LgPtolLDrZZFwowyDmUHK8zBMtOj2il49SfacJakwmqUxzVy/5D55x2ttLyPDB/wzsTOTNu4VGeeKOvE92zP8HoDb/OIOowiy0XC2kumsOvFCzfKLgB6iW18tIX9Wu2QKBgQC7HzUebY7Lt7jZu+s5U9m1oIwAVY5C8qY8Z7lNPts/aapNUTegKlQIdmB/rZqvqKBJEy6iMcxZk2/2Ftxqag/cspsxwIMQTe178EDteLctDf1DHsuqWZ3NHB23EItMoOBNLn0kz1efzOAsC5FxEWQf3cD8JielaApkXVeP7Ypm4QKBgQCwtvEI1SHAnGSc/t+y5M5l8YiTcJvYEKlVLiH5joewfcUotGtfeaXfU34GX+HbFyEgW1h8ZhBHcek9S+YiLggJkzoCfuYAiDj5c7I4RsgUvLtCnEpsKMqGLi4vMlhz92E42F6UN2RgAL789ZvDofPNLtFUAIdPmWVTqRmUxj8Ctw==',
        'log' => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('logs/wechat.log'),
        ],
    ]
];