# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Project Structure
## Users:
- 1 administrator
- 5 specialized teachers (mathematics, physics, chemistry, biology, french)
- 10 students
- 1 banned user

## Subjects:
- 5 subjects with specific descriptions
- Each subject is associated with its specialized teacher

## Chapters:
- 4 chapters per subject (20 in total)
- Specific titles for each discipline

## Exercises:
- 10 exercises per subject (50 in total)
- Thematic exercises with relevant titles
- Difficulty varies from 1 to 5

## Comments:
- 20 comments per subject (100 in total)
- Questions and remarks specific to each discipline
- Randomly distributed among students

## User Types and Authentication

### User Types
1. **Administrator**
   - Full access to all features
   - User and content management

2. **Teacher**
   - Management of assigned subjects
   - Creation and modification of exercises
   - Response to student comments

3. **Student**
   - Access to exercises
   - Ability to comment
   - Progress tracking

### Authentication
Use the following credentials to log in based on your role:

- **Admin**:
  - Email: admin@example.com
  - Password: adminpassword

- **Teacher**:
  - Email: prof_[subject]@example.com (e.g., prof_maths@example.com)
  - Password: profpassword

- **Student**:
  - Email: student[1-10]@example.com (e.g., student1@example.com)
  - Password: studentpassword

## Development

### Required Commands
Inside the PHP container, run the following commands:
```bash
php bin/console tailwind:build --watch
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
