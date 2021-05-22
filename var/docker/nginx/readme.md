Прописать корневой сертификат в системе
Установить переменную окружения CAROOT в `полный путь до директории var/docker/nginx`. Выполнить:

    mkcert --install
    
Сделать самоподписанный сертификат для localsite.ru с помощью https://mkcert.dev/

    mkcert localsite.ru
