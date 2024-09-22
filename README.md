# News Aggregator API

## Overview

The News Aggregator API provides a RESTful interface for aggregating news articles from multiple sources. It supports user authentication, article management, and personalized news feeds. The API integrates with NewsAPI and the New York Times API for a diverse range of news coverage.

## Features

- **User Authentication**: Register, login, and manage user sessions with token-based authentication.
- **Article Management**: Fetch, search, and view articles with pagination.
- **Personalized News Feed**: Retrieve news tailored to user preferences.
- **Integration**: Fetch news from NewsAPI and the New York Times API.

## Documentation

For detailed API documentation, including endpoints and usage examples, visit [SwaggerHub Documentation](https://app.swaggerhub.com/apis-docs/AFRIDIADNAN11/news-aggregator_api/1.0.0#/default/post_register).

## Getting Started

### Prerequisites

- PHP 8.3+
- Laravel 11
- Composer
- MySQL

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <repository-directory>

2. Install dependencies:
    ```bash
    composer install

3. Set up the environment:
    ```bash
    cp .env.example .env
    php artisan key:generate

4. Configure your database settings in the .env file.

5. Run migrations:
    ```bash
    php artisan migrate

6. Start the development server:
    ```bash
    php artisan serve

# Contributing

We welcome contributions to this project! To get started, please follow these steps:

1. **Fork the repository**.

2. **Create a new branch**:
   ```bash
   git checkout -b feature/YourFeature

3. **Commit your changes:**:
   ```bash
   git commit -am 'Add new feature'

4. **Push to the branch:**:
   ```bash
    git push origin feature/YourFeature

5. **Create a new Pull Request.**:
   ```bash
   git checkout -b feature/YourFeature

# Project Setup with Docker

This project can be set up using Docker and Docker Compose. Follow the instructions below to get started.

## Prerequisites

- **Docker**: Ensure Docker is installed on your machine. [Download Docker](https://www.docker.com/products/docker-desktop)
- **Docker Compose**: Ensure Docker Compose is installed. [Docker Compose Installation](https://docs.docker.com/compose/install/)

## Setting Up the Project

1. **Clone the Repository**:
   ```bash
    git clone <repository-url>
    cd <repository-directory>

2. **Start Docker Containers**:
   ```bash
    docker-compose up -d

    This command will build the Docker images and start the containers in the background.

3. **Run Migrations**:
    Execute migrations inside the Docker container:

   ```bash
    docker-compose exec app php artisan migrate

    Replace app with the name of your application service as defined in the docker-compose.yml file. This command will run the database migrations to set up the database schema.

4. **Access the Application**:

    The application should be accessible at `http://localhost:8000` or a different port specified in your `docker-compose.yml`.
