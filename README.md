# Symfony project setup:

1. First of all git clone the repo
    https://github.com/arsh91/symfony-test.git

2. Create your database by running the following command:
    ``` bin/console doctrine:database:create ```

3. Run the Migration
    ```bin/console doctrine:migrations:migrate```

4. Run the Console Command to insert data into your db
   ``` php bin/console fruits:fetch ```
