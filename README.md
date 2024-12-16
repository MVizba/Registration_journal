# Journal of Animal Treatment #

# Description

The Journal of Animal Treatment is a web application
designed to help veterinarians track the medical history
of their animal patients. It allows users to manage clients,
patients, examinations, and drug prescriptions. 
The application is built with Symfony and uses a MySQL database
for data storage.
 
# Features:

- Client Management: Users can add and manage clients (pet owners).

- Patient Management: Users can add and manage animal 
patients associated with clients.

- Examination Management: Users can assign and manage medical
examinations for patients.

- Drug Management: Users can assign drugs from a drug warehouse
to patients.

# Role-Based Access Control:

        * User Role (ROLE_USER): Veterinarians can manage clients,    
                     patients, examinations, and drugs.
    
        * Admin Role (ROLE_ADMIN): Admin users can manage all the
                      same tasks as veterinarians, plus they can 
                      add new examinations and manage the drug warehouse inventory.
      
# Prerequisites

      * Docker (for container management)
      * Composer (for dependency management)

## Installation ## 

1. Clone the Repository 

        git clone https://github.com/MVizba/Registration_journal.git
        cd <project_directory>


2. Install Dependencies

        composer install 

3. Run Docker containers
      
         docker-compose up -d

4. Run migrations: 
        
        php bin/console make:migration
        php bin/console doctrine:migrations:migrate

5. Create Admin and User Database
   - To set up the admin and user, connect to the MySQL database
     and execute the following SQL: 

   INSERT INTO registration_app.user (email, roles, password, name, last_name, position)
   VALUES
   ('admin@gmail.com', '["ROLE_ADMIN"]', '$2y$13$gGjH0xIxW28pQT8GCbiBAOQN./gsD1uhPQtYLu1f8lUhrr7ifCXgS', 'Adminas', 'Adminauskas', 'Administratorius'),
   ('user@gmail.com', '["ROLE_USER"]', '$2y$13$YJCcgn7663A1eh2ECqFvQu8oZkoTw5Xy9jihkAM5cDlxvgOPw4Moq', 'Vardenis', 'Pavardenis', 'Gydytojas');

    - admin@gmail.com hash-password Admin
    - user@gmail.com hash-password User

   You can also hash your own password using: 

   - php bin/console security:hash-password


   ## Usage

   # Roles and Permissions
   
- User Role:
      Veterinarians with this role can:
      Create new clients and patients.
      Assign examinations to patients.
      Assign drugs to patients from the drug warehouse.
- 
- Admin Role:
    Admins have all the permissions of a user, plus:
    Add new examinations.
    Manage the drug warehouse (add new drugs and edit existing ones).

#  Generating Assigned Drug Report 

Admin and User can generate a report of assigned drugs within a 
specific date range using Symfony Console. 

- php bin/console app:drug-report <start_date> <end_date>
  php bin/console app:drug-report 2021-01-01 2024-12-31


Code Quality Tools used: 

- phpstan:
    vendor/bin/phpstan analyse

- php-cs-fixer

- phpmd:
  vendor/bin/phpmd src/ text cleancode,codesize,unusedcode 