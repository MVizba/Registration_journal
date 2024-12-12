
1. composer install 
2. Run containers


3. Run migrations: 

php bin/console make:migration

php bin/console doctrine:migrations:migrate

4. Connect to database and create a user in it with admin ad user roles. 

for hash password try to use command in docker container:
- php bin/console security:hash-password 
- admin password is Admin.
- user password is User. 

INSERT INTO registration_app.user (email, roles, password, name, last_name, position)
VALUES ('admin@gmail.com','["ROLE_ADMIN"]', '$2y$13$gGjH0xIxW28pQT8GCbiBAOQN./gsD1uhPQtYLu1f8lUhrr7ifCXgS', 'Adminas', 'Adminauskas', 'Administratorius'),
('user@gmail.com','["ROLE_USER"]','$2y$13$YJCcgn7663A1eh2ECqFvQu8oZkoTw5Xy9jihkAM5cDlxvgOPw4Moq', 'Vardenis', 'Pavardenis','Gydytojas');