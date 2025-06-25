## Project Setup

### Requirements
- Docker
- PHP 8.4.5

### Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/allvv/Curotec-programming-test.git
   cd Curotec-programming-test

2. Install dependencies and setup Laravel Sail:
    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate

3. Update the variables in the .env file so that they match your local setup. 

4. Start Docker and run Sail:
    ```bash
    ./vendor/bin/sail up -d
 
5. Run migrations and seeders:
    ```bash
    ./vendor/bin/sail artisan migrate --seed