### Customer that deposit money and can withdraw, Report included

- Clone the repo.
- run following commands:

>composer install
>php artisan key:generate
>copy .env.example to .env
>create database for the project and run:
>php artisan migrate

## Tasks

- run "php artisan db:seed" to create random customers with random bonus and unique mail.
- run "php artisan serve" to get ready website http://localhost:8000/api
- routes
    - Login - ``Login with email, password and get back token``
    - Register - ``Use Customer info with password to generate new customer``
    - editCustomer/{$id} - ``Use new Customer info to update customer``
    - deposit/{$id}/{$sum} - ``Use the customer id to add sum, and on every 3rd deposit customer receives bonus``
    - withdraw{$id}/{$sum} - ``Use the customer id to withdraw sum, make sure you dont get negative balance.``
    - report - ``Generate report of number of withdraws,deposits and total deposits/withdraws``
