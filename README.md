# Laravel Backend for Ollama (AI Chat Interface)

This project provides a sleek, modern web-based chat interface for interacting with local LLMs (Large Language Models) powered by [Ollama](https://ollama.com/). It is built on the robust Laravel framework, utilizing Tailwind CSS for beautiful, responsive styling and Vite for fast frontend asset bundling.

![Screenshot of the AI Chat Interface](https://github.com/laravel/art/raw/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

## 🌟 Key Features

*   **Local AI Integration:** Seamlessly connect directly to your local Ollama instance (`http://127.0.0.1:11434` by default).
*   **Real-time Streaming:** Features fully asynchronous Server-Sent Events (SSE) streaming for AI responses—seeing words appear as they are generated, just like ChatGPT.
*   **Chat History Management:** Conversations are organized into distinct "Sessions" using a persistent SQLite backend database. View past history easily.
*   **Model Selection:** Switch seamlessly between installed Ollama models (TinyLlama, Llama3, Mistral, Qwen, Gemma, etc.) directly from the dropdown.
*   **Markdown Support:** Code blocks, syntax highlighting, and general markdown formatting work beautifully out-of-the-box thanks to a custom JavaScript parser.
*   **Responsive UI:** A premium dark-mode interface designed with Tailwind CSS that works beautifully on both desktop and mobile screens.

## 🛠️ Technology Stack

*   **Backend:** PHP 8.2+, Laravel 11.x
*   **Frontend:** HTML5, Alpine / Vanilla JS, Blade Templates
*   **Styling:** Tailwind CSS 3.4
*   **Database:** SQLite (Default for rapid setup)
*   **Build Tool:** Vite

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine.

### Prerequisites

You will need the following installed on your system:
*   [PHP](https://www.php.net/) (v8.2 or higher)
*   [Composer](https://getcomposer.org/) (for managing PHP dependencies)
*   [Node.js](https://nodejs.org/) and NPM (for compiling Tailwind CSS via Vite)
*   [Ollama](https://ollama.com/) (running natively on your machine)

### 1. Configure Ollama

Ensure Ollama is running completely in the background. If you plan to use models like `tinyllama`, pull them down first:
```bash
ollama pull tinyllama
ollama pull llama3
```

**Cross-Origin Notice:** By default, Laravel talks to Ollama via server-to-server internal HTTP requests using Guzzle, avoiding browser CORS issues, meaning no special Ollama environment variables are immediately necessary for local development unless on a separate server.

### 2. Clone and Setup

Clone the repository and install all required dependencies.

```bash
git clone <your-repo-url> laravel-backend-for-ollama
cd laravel-backend-for-ollama

# Install PHP packages
composer install

# Install NPM packages
npm install
```

### 3. Environment configuration

Copy the example environment file:
```bash
cp .env.example .env
```

Generate a new application key:
```bash
php artisan key:generate
```

Open `.env` and verify the Ollama API endpoint matches your local setup (usually correct by default!):
```env
OLLAMA_URL=http://127.0.0.1:11434
```

### 4. Database Setup

This project uses an SQLite database by default. Run the database migrations (this will create `database/database.sqlite` automatically in Laravel 11+):

```bash
php artisan migrate
```

### 5. Start the Application

You need two terminal windows running side-by-side to serve both PHP side routing and Vite asset compilation.

**Terminal 1 (Vite & Tailwind CSS):**
```bash
npm run dev
```

**Terminal 2 (Laravel Backend server):**
```bash
php artisan serve
```

### 6. Usage

Open your web browser and navigate to `http://127.0.0.1:8000`. 
1. Register a new user account or log in.
2. Ensure you have the proper local AI model selected in the top dropdown.
3. Start typing and chatting!

## 📂 Architecture Context

*   `app/Http/Controllers/ChatController.php` - The heart of the application. It handles sending history contexts to the Ollama API, streaming the responses back over SSE, and saving messages.
*   `app/Models/ChatSession.php` & `ChatMessage.php` - Eloquent models for relationship structures between users, their chat threads, and individual texts.
*   `resources/views/chat.blade.php` - The primary GUI view, containing both the HTML layout and the Vanilla JS client-side DOM manipulation logic.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
