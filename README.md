
# Simplified Banking API

Neste projeto foi feito uma API Rest com Laravel para uma plataforma de pagamentos simplificada. Nela é possível depositar e realizar transferências de dinheiro entre usuários. Temos 2 tipos de usuários, os comuns e lojistas, ambos têm carteira com dinheiro e realizam transferências entre eles.

### Tecnologias usadas:

* Docker
* PHP (v8.3)
* Laravel (v11)
* MySQL
* PHPUnit
* Swagger

### Requisitos

* Ter o Docker instalado na máquina
* Ter no mínimo a versão 8.3 do PHP instalado
* PHPUnit versão ^11.0
* MySQL

### Intalação

Para rodar o projeto é preciso entrar na pasta do mesmo e rodar

```
docker compose up --build -d
```

Acessar container da aplicação, atualizar dependências, subir migrations e seeders:

```
docker compose exec app bash

composer install

php artisan migration

php artisan db:seed

php artisan optimize:clear

```

Portas usadas para os serviços foram as seguintes:

* Nginx: 8000
* MySQL: 3300
* PHPMyAdmin: 8080

### Funcionamento

Feita as atualizações e instalação acima, ao acessar a rota /api/ é possível verificar a conexão com o banco e o uso da memória.

A rota /transfer é o onde a lógica do negócio acontece, em que o pagador realiza uma transação bancária, é verificado se o mesmo não é lojista (não possui permissão para transferência) e possui saldo na carteira. Além disso, verifica-se a conexão de um serviço externo para efetuar a transação, caso não ocorra ou tenha falha o dinheiro volta para a carteira do pagador. Após ter sucesso a transação é disparado uma notificação de transação efetuada com sucesso.

Nos testes esse passo a passo do pagamento é testado de forma mais robusta para que não aconteça erros na transação.
