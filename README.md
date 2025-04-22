
# Apotek Arrozaq

Drug stock management system in pharmacies.


## Important

    1. Make sure you have created a database first.
    2. Make sure you have a web server and database running. Example: xampp, or laragon.
    3. env is set when you have completed the Run Locally stage in the Copy env file to .env step.
    4. To set the .env file, just look for the variable and uncommand the line. To set the database, uncommand everything in "database.default.xxx"
## Environment Variables

To run this project, you will need to add the following environment variables to your .env file.

`CI_ENVIRONMENT=development`

`app.baseURL=http://localhost:8080`

`database.default.database=your_database_name`

`database.default.username=your_database_username`

`database.default.password=your_database_password`


## Run Locally

Clone the project

```bash
  git clone https://github.com/rizkyalfito/apotek-arrozaq.git
```

Go to the project directory

```bash
  cd apotek-arrozaq
```

Install dependencies

```bash
  composer install
```

Copy env file to .env

```bash
  cp env .env
```

Run migrations

```bash
  php spark migrate:refresh
```

Seeding database

```bash
  php spark db:seed UserSeeder
```

Start the server

```bash
  php spark serve
```

