# Project Setup and Running Instructions

## Overview

This project uses Docker to set up a PHP development environment with Apache. The Dockerfile provided sets up PHP 8.2 with necessary extensions and Composer for managing PHP dependencies.

## Prerequisites

- **Docker**: Ensure Docker is installed on your machine. You can download it from [Docker's official website](https://www.docker.com/products/docker-desktop).

## Setup Instructions

1. **Clone the Repository**

   If you haven't already, clone the repository to your local machine:

   ```bash
   git clone https://github.com/shoaib7895/News-Aggregator-API.git
   cd News-Aggregator-API

2. **Build and Start the Services**

  Use Docker Compose to build the Docker image and start the container. This will also start any other services defined in docker-compose.yml.
  ```bash
  docker-compose up --build
  ```
 The --build flag ensures that the Docker image is rebuilt according to the Dockerfile changes.

3. **Access the Application**

  Open your web browser and go to http://localhost:80.
  You should see your Laravel application running.

4. **Managing the Container**  
    To stop the container, use:
    ```bash
    docker-compose down
    ```
    To restart the container, use:
     ```bash
     docker-compose up -d
     ```
    To view logs:
    ```bash
    docker-compose logs
    ```
    To run a command in the running container, use:
    ```bash
    docker-compose exec web bash
    ```


# API Documentation
For API documentation, including interactive API documentation and endpoint details, please visit http://localhost:80/api/documentation after starting the Docker environment. This link points to the Swagger documentation hosted within the Docker container.   