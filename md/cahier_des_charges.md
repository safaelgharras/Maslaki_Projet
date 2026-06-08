# Cahier des Charges Simplifié — Projet Maslaki

Ce document explique simplement ce qu'est le projet **Maslaki**, ses fonctionnalités, son design et son fonctionnement technique.

---

## 1. C'est quoi Maslaki ?

### 1.1 Le But
Aider les étudiants et futurs bacheliers au Maroc à trouver facilement leur école supérieure, à s'orienter grâce à une intelligence artificielle (IA), et à lire des avis d'autres étudiants.

### 1.2 Pour qui ?
*   **Les Étudiants :** Qui cherchent des écoles, sauvegardent des favoris, demandent de l'aide à l'IA ou prennent des rendez-vous.
*   **Les Administrateurs :** Qui gèrent la plateforme (modération des avis, envoi de notifications, gestion des accès).

---

## 2. Que fait la plateforme ? (Fonctionnalités)

### 2.1 Pour les Étudiants

*   **Inscription et Connexion faciles :**
    *   Création de compte classique (email et mot de passe).
    *   Connexion rapide avec un compte Google.
*   **Recherche intelligente d'écoles :**
    *   Recherche par mot-clé (nom d'école, spécialité).
    *   Filtres simples par Ville, Secteur (Public/Privé), Filière, ou Type de Bac.
*   **Fiches des Écoles :**
    *   Détails complets : seuils d'accès, diplômes préparés, durée des études, et site officiel.
    *   Espace d'avis partagés par d'autres étudiants.
    *   Bouton pour sauvegarder l'école dans ses favoris.
*   **Orientation par IA :**
    *   Un formulaire rapide (Bac, moyenne, ville préférée).
    *   L'IA propose instantanément les écoles adaptées au profil de l'étudiant.
*   **Rendez-vous et Notifications :**
    *   Possibilité de réserver un créneau de discussion avec un conseiller d'orientation.
    *   Alertes en temps réel (cloche de notifications et messages éphémères à l'écran).

### 2.2 Pour les Administrateurs

*   **Tableau de bord :** Un espace privé pour piloter le site.
*   **Modération des avis :** Validation ou suppression des avis écrits par les étudiants avant leur publication.
*   **Envoi de messages :** Envoi d'annonces à tout le monde ou de messages privés à un étudiant précis.
*   **Gestion des rôles :**
    *   Possibilité de nommer un autre utilisateur "administrateur" ou de lui retirer ce rôle.
    *   Sécurité pour éviter qu'un administrateur ne supprime ses propres accès par erreur.

---

## 3. Le Design et l'Expérience (UI/UX)

*   **Couleurs :** Une identité visuelle moderne basée sur le **Bleu Navy** (sérieux, professionnel) et l'**Orange** (dynamique, appels à l'action).
*   **Mode Sombre (Dark Mode) :** Un bouton pour basculer facilement entre le thème clair et sombre.
*   **Adapté aux mobiles :** Le site s'affiche parfaitement sur téléphone, tablette et ordinateur.
*   **Trois langues :** Disponible en **Français**, **Anglais** et **Arabe** (avec inversion automatique du sens de lecture pour l'Arabe).

---

## 4. Technique et Sécurité (En termes simples)

### 4.1 Sécurité
*   **Mots de passe protégés :** Cryptés de manière à ce que personne ne puisse les lire en clair.
*   **Protection du site :** Systèmes de sécurité contre le piratage de données (protection CSRF et XSS).
*   **Accès sécurisé :** Seuls les utilisateurs connectés et autorisés peuvent accéder aux pages d'administration.

### 4.2 Technologies utilisées
*   **PHP 8 & MySQL :** Le moteur et la base de données de la plateforme.
*   **Fichier `.env` :** Les clés secrètes du site sont cachées dans un fichier sécurisé à part.
