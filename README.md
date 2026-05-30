# ISET Clubs 🎓
Plateforme de gestion des événements et recrutements des clubs ISET Zaghouan.

## Stack
- PHP 8.2 / Symfony 7.4
- Twig / Doctrine ORM / MySQL
- JWT Authentication / Symfony Messenger

## Équipe
- Dev A — Auth & Sécurité
- Dev B — Clubs & Membres  
- Dev C — Événements & Feedbacks
- Dev D — Recrutement & Admin

## Comptes de test
- Admin     : admin@iset.tn     / admin123
- Président : president@iset.tn / president123
- Étudiant  : etudiant@iset.tn  / etudiant123

## Installation
1. git clone https://github.com/TON_USERNAME/iset-clubs.git
2. cd iset-clubs
3. composer install
4. cp .env .env.local (configure DATABASE_URL)
5. php bin/console doctrine:database:create
6. php bin/console doctrine:migrations:migrate
7. php bin/console doctrine:fixtures:load
8. symfony server:start

## Année universitaire 2025-2026
Encadré par : RAMZI GHAZOUANI
