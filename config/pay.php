<?php

return [
    'alipay' => [
        'app_id' => '2016092300580730',
        'ali_public_key' =>
            'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuCOOmF8F7TQgWx506lB5hjv36EBFI1hQS9rOevqX8Sz5+xsHgEeIyEl4BrsUd0uiR15yjOf+ruoXAnvHYu0kzqv5h7dNeio6S7NuqG3A9DoDCFiVzVTNlHfQEpZKZaLqTE08YogYIyOMkthaY3BViGKafzpe683cXdxn2FTTURgDQvqDWup28KZHtbIkw0Cw+RbKRVwHe0M9h9G8+0WzX/sd+qncyS6z7PvAJMI4MM5aFB2co2Otr0wRsmIESA6DSFse1LXVxPa/Kq3jzULa3O366eGcEDRmtnCJEDLRIJgI/HWVOlRsgFTqeHfix4pEPtSaeoyjIhx1sguHxyQSowIDAQAB',
        'private_key' =>
            'MIIEogIBAAKCAQEAvgLSYQCVeZxe5/3Qsratkr4drqJEvJuFZf3pYYXL3qy/Ma/V3hIGnQKAQolUXzZRm6UibETGrqeeij+eG9xyAJWw5JVFpxpO4cT3pJHgCRcq6L2kHsn3SnfCJSbMdIRmXgVdV0hZpFyWU3HfNsU/E+t/fbDggybIj0l+cKSMSg7HvhY0MaAlvzNJAa2EXB4GZameC2QxJIrg34Z0XaluyAf08iwUVvuMAUl1SqTgXc0hENjC/TiICVAxjmQPMdOATryYz4Q8rO/miwVM0UOOukqWqLVnCXgIij542FuRYMOf7dsbnoPs+lqIe9UMB1UKJ0azxWRYJZQE4EFnrgu+DQIDAQABAoIBADVFiUIU7dNomdsk+AwC12saziuV5vuSBrZvl6z13BCUEg6WI7ndB+mhN+BvrC39hqfChoIgTivNZs+Pwn5BMd0kGrIbf6m34IG/vUZwd4VU73zNHmRbb297X/0WgZ/xNtoJWwALksZYBvViKZireGHXVqkgm/FpQDWb/00JdSOVwYD/7NKvTjoi+HdTxUAakss4/RXMdP64U8UQQICgzPC/ZJcNFemkfNga/Z7d6sS8xXNdsiwAQPWJxaC8LtQFegHeyRq9LsO0v16KzitemQ01XSlCxuoLzqG535WipcPaou+WtxHGbh0xxCTNy3/gym8MLq4dvcw4A8GizeIrTkECgYEA5B57JSLXD+sylN5HU7W/BUMmFicAVxTVUkaaPd1D3xXWqvA+gG5E8Op8fsTfXqwtFQq8FkUKNRhxhY1QnblM9SP39Mc1uvPvkg9ZRHp8iIGGsDxqH2MwA0X8u2nkCwg5TjWa40KytR5FNWzdkfFzES0NJX9Xl+Czp0nL5l83QLUCgYEA1Tv+n+TYnxnmh5PG/JIy78cI1by+bm21d3Mb5ZFnhJWDqysP3eG2p57JUQDxe7/XzZpzpjQCVsLo05gNvKYjF2UpwD0p17sgOJJhQnbBnM80f9BN10/40UazsQKm9cz8BvQcWR8iHPmJ7DoacZ5ygaHFcbUWejAeiKUNfXLtVvkCgYBKzFfSoG7mMFegipXWs+RTpGXro5Qv+YvM84uFt3SWxIFkAWxtDjsax7hUlNctIsWRfbiYkMC9Eiu+/8wsO2Mpika2g09x4qRuPwwlMQh+dgIk7Vpaulo26I7rINh+aY7ovxjvZaJVUvycfPrV7NavDheecPfWu/4MX101R4lFdQKBgAbxeysNspgLqdwETdhvkkUIgBslmDXUULJhBymEgJBqpezu7AdXkDEJFJkUpMhYyNgjDVz+GI1mr1oke14HMuFSI/fkhfZGW5g7+/rEDy3h+V2oFMDME9gMq1E6OuGKGTLIxBuKzfuPjJQqmC3W8PeTovA+60pzQqtEZJoPIfdRAoGAR7kA8cbRC7LS8uEsU62E6MZBPEAZDHYoRO/4xVre53I5xkFxjJE6xzrRErqPGCaw4TDhinTDCcCqNXqwW++nfZfbKdPyjbY/H7AkKkbpFNdllVGBY9HhRUjQh26EchsC80XBIGVgRPg4wgw1yfaTDh2oJ5mCqYrR5efvnVbekUY=',
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