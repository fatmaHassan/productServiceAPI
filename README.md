# Native Instruments Test Task -  Product service API

A REST API based Product service with following endpoints:
- http://localhost/products  (GET Method)
- http://localhost/user (GET Method)
- http://localhost/user/products (GET Method)
- http://localhost/user/products/{sku} (DELETE Method)
- http://localhost/user/products (POST Method)
- http://localhost/auth (POST Method)

## Implementation

- Docker based environment, with php7.4, mysql, apache, docker-compose.yml file to start all services once.
- Sympfony5 Framework is used for the implementation.
- Used "Doctrine ORM" to handle the Database entities (User, Product, Purchased), Entity files found under /src/Entity.
- Used "DoctrineFixturesBundle" to load data from provided csv files into database tables, a how-to command is listed afterwards under "How to Install section".
- Used  "lexik/jwt-authentication-bundle" to generate and manage the JWT functionality.
- Implemented some basic unit tests using PHPUnit Lib under /tests


## How to install and run the Application

- From root of the folder, Build and run docker containers using commands listed below, please consider same order:
 - ```bash
   - docker-compose build [build images]
   - docker-compose up -d [run containers]
   - docker ps [see running containers]
   ```

- Login inside the php docker container by running the following command :
 - ```bash
    docker exec -it -u dev php bash 
   ```
- From inside the php container, run the following commands to install dependencies, create, populate the db.
 - ```bash
   - composer install [ install dependencies listed in composer.json file ]
   - php bin/console doctrine:database:create [ create the db, db name is an env variable, in .env file, under DATABASE_URL ]
   - php bin/console doctrine:migrations:migrate [ run migrations ]
   - php bin/console doctrine:fixtures:load [ Load fixtures found in "/src/DataFixtures/data" to populate database tables ]
   ```

- Generate SSH keys, required by "lexik/jwt-authentication-bundle" by running following commands:
 - ```bash
   mkdir config/jwt
   openssl genrsa -out config/jwt/private.pem -aes256 4096
   openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
      ```
   
  - notice that a secret key will be asked from you in the previous two commands, and this is stored in .env file, under (JWT_PASSPHRASE), so you should change that to whatever you choose.

- using any tool to test the API eg. Postmann, open (http://localhost/auth) link, with the following settings (POST method, choose Body of type row in the following format 
{
"username": "valid-user-email"
"password": secret
}
- the result is a jwt token which is passed in the header of all other endpoints to authorize them.
 - ![Alt text](public/images/successAuth.png?raw=true  "successful obtaining of a token")

- notice that if you pass invalid user credentials, the result is 401 unauthorized response, please check the following screenshot
  - ![Alt text](public/images/failAuth.png?raw=true  "failed Authentication")


- Check [ http://localhost/user/products (GET Method) ] , by passing Headers [content-type: json, Authorization: Bearer token] , please check the following screenshot from postmann
 - ![Alt text](public/images/Allproducts.png?raw=true  "fetch all products")
 
 ## How to run unit tests via CLI
 - login inside the PHP docker container by running the following command from CLI
  - ```bash
       docker exec -it -u dev php bash 
      ```
    
 - then run the follwoing command 
  - ```bash
           php bin/phpunit
      ```
    
 - Please check the following screenshot
   - ![Alt text](public/images/unittests.png?raw=true  "run unit tests")
   
 
## Optimisation points

- Add more unit or functional tests for the Controllers, since time given only was enough to implement some repository tests.


