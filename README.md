 <div align="center">

# CampusGov

### Student Organisation Management, Simplified.

**A Web-Based Management System for Student Union Governments and Associations**

<p>
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 4">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<p>
  <a href="https://github.com/jamesefekodo/campusGov">Repository</a> •
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#technology-stack">Technology Stack</a>
</p>

</div>

---

## About CampusGov

**CampusGov** is an integrated web-based management platform designed to simplify the administration of Student Union Governments (SUGs), departmental associations, faculty associations, and other student-led organisations.

It provides a centralised digital environment for managing membership records, organisational structures, student leadership, elections, financial activities, events, publications, academic resources, and administrative documentation.

Developed using **NACOMES (National Association of Computer Engineering Students), OGITECH Chapter**, as a case study, CampusGov adopts a flexible organisational management approach intended to support different student organisations.

## Features

CampusGov brings essential student organisation management functions into one platform.

<table>
<tr>
<td width="50%" valign="top">

### 👥 Membership Management

- Member registration and accounts
- Personal and academic profiles
- Academic history
- Skills and employment records
- Awards and leadership experience
- Social media profiles
- Membership verification

</td>
<td width="50%" valign="top">

### 🏛️ Organisational Administration

- Multi-tenant organisation structure
- Faculty and department management
- Academic programmes
- Administrative profiles
- Organisational policies
- System configuration

</td>
</tr>
<tr>
<td width="50%" valign="top">

### 🔐 Roles and Permissions

- Root administrator privileges
- Role-based access control
- Granular permission assignment
- Restricted administrative access
- Session-based administrative access management

</td>
<td width="50%" valign="top">

### 🗳️ Election Management

- Election administration
- Election positions
- Voter eligibility groups
- Candidate applications
- Standard voting records
- Monetary voting records

</td>
</tr>
<tr>
<td width="50%" valign="top">

### 💰 Financial Management

- Association dues
- Payment methods
- Electronic payment configuration
- Payment transactions
- Payment regularisation
- Payment waivers
- Invoices
- Financial claims
- Donations

</td>
<td width="50%" valign="top">

### 🎓 Student Leadership

- Leadership arms
- Leadership positions
- Leadership tenures
- Representative assignments
- Association advisers

</td>
</tr>
<tr>
<td width="50%" valign="top">

### 📰 Events and Publications

- Event management
- Media publications
- Publication categorisation
- Tags and classifications
- Likes and comments
- Publication approval

</td>
<td width="50%" valign="top">

### 📚 Academic Resources

- Past questions
- Past question contributions
- Recommended books
- Recommended courses
- Industry affiliates
- Resource categorisation

</td>
</tr>
</table>

### 📁 Organisational Archives

Centralised management of organisational documents and records to support information preservation and administrative continuity.

---

## Technology Stack

CampusGov is developed using modern web technologies and frameworks.

| Technology | Application |
|:---|:---|
| **PHP 8.3+** | Backend programming |
| **Laravel 13** | Application framework |
| **Livewire 4** | Interactive application components |
| **MySQL** | Relational database |
| **HTML5** | Web structure |
| **CSS3** | Interface styling |
| **JavaScript** | Client-side interactions |
| **Composer** | PHP dependency management |
| **Node.js / npm** | Frontend asset management |
| **mPDF** | PDF document generation |

## System Architecture

CampusGov adopts a layered web application architecture.

```text
              USERS
                |
                v
       PRESENTATION LAYER
      Web Interface / Livewire
                |
                v
       APPLICATION LAYER
     Laravel / Business Logic
       Authentication / Access
                |
                v
          DATA LAYER
         MySQL Database
```

The application uses Laravel's MVC architecture, with Livewire providing interactive user interface components.

## Access Control

CampusGov uses a role-based access control structure to manage system privileges.

| User Category | Access Level |
|:---|:---|
| Root Administrator | Unrestricted system access |
| Administrators | Access based on assigned roles and permissions |
| Members with Administrative Roles | Access limited to assigned administrative privileges |
| General Members | Member-facing functions and personal profile management |

Administrative privileges can be reassigned by the root administrator in accordance with academic session arrangements.

---

## Installation

### Requirements

Ensure the following are installed:

- PHP 8.3 or later
- Composer
- MySQL
- Node.js and npm
- Apache, Nginx, or PHP development server

### 1. Clone the Repository

```bash
git clone https://github.com/jamesefekodo/campusGov.git

cd campusGov
```

### 2. Install Dependencies

```bash
composer install

npm install
```

### 3. Configure Environment

```bash
cp .env.example .env

php artisan key:generate
```

On Windows Command Prompt:

```cmd
copy .env.example .env
```

### 4. Configure Database

Create a MySQL database and update the database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campusgov
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations

```bash
php artisan migrate
```

Run seeders if required by the installation:

```bash
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
npm run build
```

For development:

```bash
npm run dev
```

### 7. Configure Storage

```bash
php artisan storage:link
```

### 8. Start the Application

```bash
php artisan serve
```

Access the application at:

```text
http://127.0.0.1:8000
```

> **Note:** These are standard Laravel installation steps. Additional application-specific configuration, database seeding, and service credentials may be required.

---

## Project Structure

```text
campusGov/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Livewire/
│   ├── Models/
│   └── Providers/
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── public/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
│
├── routes/
├── storage/
├── tests/
│
├── artisan
├── composer.json
├── package.json
└── README.md
```

## Intended Applications

CampusGov is designed for:

- Student Union Governments
- Departmental associations
- Faculty-based student associations
- Professional student organisations
- Other student-led organisations

Its organisational structure is intended to make it adaptable beyond the original NACOMES case study.

---

## Contributing

Contributions, suggestions, bug reports, and improvements are welcome.

If you would like to contribute:

1. Fork the repository.
2. Create a feature branch.
3. Implement your changes.
4. Submit a pull request.

---

## Author

<div align="center">

**James Oghenekevwe Efekodo**

Software Developer | System Architect

[GitHub Profile](https://github.com/jamesefekodo)

</div>

---

<div align="center">

**CampusGov**

*Building a more organised and digitally empowered student administration.*

<sub>Developed with Laravel and Livewire.</sub>

</div>

---

## License

No license has currently been specified. Refer to the repository owner for permission to use, modify, or redistribute the project.

