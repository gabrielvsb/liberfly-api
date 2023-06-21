## Configurações Iniciais

Para o correto funcionamento da aplicação, é necesspario ter o docker instalado.

Apesar de não ser o adequado, mas para ser mais rápida e eficaz a correção, deixei disponibilizado no git o arquivo .env

Após clonar o repositório em sua pasta de preferencia, entre na mesma e realize os comandos a seguir:

```
docker-compose up -d
```
Em seguida utilize o comando para entrar no terminal do servidor:
```
docker exec -it liberfly-api bash
```

Dentro do terminal, execute o comando a seguir para instalar as dependências do projeto:
```
composer install
```

Após finalizar a instalação das dependencias do projeto, execute os comandos:
```
php artisan key:generate
```
```
php artisan migrate
```
```
php artisan jwt:secret
```
