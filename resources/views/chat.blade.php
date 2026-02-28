@extends('layouts.app')

@section('content')
    <div class="flex h-screen w-full overflow-hidden bg-slate-900 relative">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop"
            class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-20 hidden md:hidden transition-opacity opacity-0"></div>

        <!-- Sidebar -->
        <div id="sidebar"
            class="shrink-0 w-72 bg-slate-950 border-r border-slate-800/50 flex flex-col shadow-xl z-30 fixed inset-y-0 left-0 md:relative transition-transform duration-300 -translate-x-full md:translate-x-0">
            <div class="p-5 border-b border-slate-800 flex justify-between items-center">
                <h1
                    class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Ollama Chat
                </h1>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button title="Logout" type="submit" class="text-slate-500 hover:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                    <button id="closeSidebarBtn" class="md:hidden text-slate-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <button id="newChatBtn"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-indigo-500/30 bg-indigo-600/10 hover:bg-indigo-600/20 px-4 py-2.5 text-sm font-semibold text-indigo-300 shadow-sm transition-all hover:scale-[1.02]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Chat
                </button>
            </div>
            <div class="mt-2 px-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                History
            </div>
            <div class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar pb-4" id="sessionList">
                @foreach($sessions as $session)
                    <div data-id="{{ $session->id }}"
                        class="session-wrapper relative group w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:bg-slate-800/60 hover:text-white transition-all {{ $loop->first ? 'bg-slate-800 text-white font-medium active' : '' }}">
                        <button
                            class="session-btn group-2 flex-1 flex items-center gap-3 truncate border-none outline-none bg-transparent">
                            <svg class="w-4 h-4 shrink-0 transition-opacity {{ $loop->first ? 'text-indigo-400 opacity-100' : 'opacity-50 group-hover:text-indigo-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            <span class="truncate text-left w-full pointer-events-none">{{ $session->name }}</span>
                        </button>
                        <div
                            class="absolute right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800/80 px-1 rounded-md">
                            <button class="rename-btn p-1 text-slate-400 hover:text-indigo-400" title="Rename"><svg
                                    class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg></button>
                            <button class="delete-btn p-1 text-slate-400 hover:text-red-400" title="Delete"><svg
                                    class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col relative bg-slate-900 min-w-0 h-full w-full">
            <!-- Header -->
            <div
                class="h-16 px-4 md:px-6 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/80 backdrop-blur-md absolute top-0 w-full z-10 shadow-sm">
                <div class="flex items-center gap-2 md:gap-3">
                    <button id="openSidebarBtn" class="md:hidden text-slate-400 hover:text-white mr-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="hidden sm:inline font-semibold text-slate-200 text-sm tracking-wide">Local AI Model</span>

                    <div class="relative ml-0 sm:ml-2">
                        <select id="modelSelect"
                            class="appearance-none bg-slate-800/80 border border-slate-700/80 rounded-lg text-xs font-medium text-slate-300 py-1.5 pl-3 pr-8 hover:bg-slate-800 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer outline-none transition">
                            <option value="tinyllama" selected>tinyllama</option>
                            <option value="llama3.2:3b">llama3.2:3b</option>
                            <option value="llama3">llama3</option>
                            <option value="llama2">llama2</option>
                            <option value="mistral">mistral</option>
                            <option value="qwen">qwen</option>
                            <option value="gemma">gemma</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="hidden md:inline text-slate-400">Logged in as</span>
                    <div
                        class="flex items-center gap-2 bg-slate-800/50 rounded-full pl-1 pr-3 py-1 border border-slate-700/50">
                        <div
                            class="bg-indigo-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}</div>
                        <span class="text-slate-200 font-medium">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>

            <div id="chatHistory"
                class="flex-1 overflow-y-auto overflow-x-hidden p-6 pt-24 pb-32 space-y-6 scroll-smooth custom-scrollbar">
                <!-- Messages go here -->
            </div>

            <div
                class="px-6 pb-6 pt-4 bg-gradient-to-t from-slate-900 via-slate-900 to-transparent absolute bottom-0 w-full">
                <div class="max-w-4xl mx-auto relative group">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl opacity-20 group-hover:opacity-40 transition duration-500 blur">
                    </div>
                    <div class="relative bg-slate-800 rounded-2xl border border-slate-700 shadow-xl flex items-end">
                        <textarea id="chatInput" rows="1"
                            class="w-full max-h-48 resize-none bg-transparent pl-5 pr-12 py-4 text-[15px] leading-relaxed text-white placeholder-slate-400 focus:outline-none focus:ring-0 border-none rounded-2xl"
                            placeholder="Send a message to Ollama... (Shift+Enter for newline)"></textarea>
                        <button id="sendBtn"
                            class="absolute right-3 bottom-2.5 rounded-xl p-2 bg-indigo-600/90 text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all disabled:opacity-50 disabled:hover:scale-100 disabled:hover:bg-indigo-600/90">
                            <svg class="w-5 h-5 -ml-0.5 mt-0.5 transform rotate-[-20deg]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                    <div
                        class="text-center mt-2 text-[10px] text-slate-500 font-medium tracking-wide w-full flex justify-center opacity-70">
                        AI responses can make mistakes. Verify important information.
                    </div>
                </div>
            </div>
        </div>
    </div>

    </template>

    <template id="userMsgTemplate">
        <div class="flex justify-end max-w-4xl mx-auto w-full mb-6 relative px-2 sm:px-0">
            <div
                class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 sm:px-5 py-3 sm:py-3.5 text-[14px] sm:text-[15px] leading-relaxed shadow-sm min-w-0 max-w-[85%] sm:max-w-[75%] break-words html-content">
            </div>
        </div>
    </template>

    <template id="aiMsgTemplate">
        <div class="flex justify-start max-w-4xl mx-auto w-full group mb-6 relative px-2 sm:px-0">
            <div
                class="flex-shrink-0 h-8 w-8 sm:h-9 sm:w-9 mt-1 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm mr-3 sm:mr-4 relative">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
            </div>
            <div
                class="relative bg-slate-800/80 text-slate-200 border border-slate-700/80 rounded-2xl rounded-tl-sm px-4 sm:px-6 py-3 sm:py-4 pb-10 text-[14px] sm:text-[15px] leading-relaxed shadow-sm min-w-0 max-w-[85%] sm:max-w-[85%] break-words prose prose-invert prose-indigo prose-pre:bg-slate-900 prose-pre:border prose-pre:border-slate-700 w-full overflow-x-hidden">
                <div class="html-content w-full overflow-x-auto custom-scrollbar"></div>
                <button
                    class="copy-btn absolute bottom-2 right-2 p-1.5 text-slate-400 hover:text-indigo-400 bg-slate-800/50 hover:bg-slate-700/50 rounded-lg transition-all opacity-0 group-hover:opacity-100 flex items-center gap-1.5 text-[11px] font-medium tracking-wide border border-transparent hover:border-slate-600/50"
                    title="Copy text">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Copy
                </button>
            </div>
        </div>
    </template>

    <style>
        /* Custom scrollbar to make it look premium */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentSessionId = {{ $sessions->first()->id ?? 'null' }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const openSidebarBtn = document.getElementById('openSidebarBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');

            const chatHistory = document.getElementById('chatHistory');
            const chatInput = document.getElementById('chatInput');
            const sendBtn = document.getElementById('sendBtn');
            const sessionList = document.getElementById('sessionList');
            const newChatBtn = document.getElementById('newChatBtn');
            const userMsgTemplate = document.getElementById('userMsgTemplate');
            const aiMsgTemplate = document.getElementById('aiMsgTemplate');

            // Sidebar Toggle Logic
            const toggleSidebar = () => {
                const isMobileClosed = sidebar.classList.contains('-translate-x-full');
                
                if (isMobileClosed) {
                    // Open sidebar (Mobile)
                    sidebarBackdrop.classList.remove('hidden', 'pointer-events-none');
                    // trigger reflow
                    void sidebarBackdrop.offsetWidth;
                    sidebarBackdrop.classList.remove('opacity-0');
                    sidebarBackdrop.classList.add('opacity-100');
                    
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                } else {
                    // Close sidebar (Mobile/Desktop)
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.remove('md:translate-x-0'); // Add this to force close on desktop if desired, though normally hamburger is hide/show on mobile only
                    sidebar.classList.add('-translate-x-full');
                    
                    sidebarBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    sidebarBackdrop.classList.remove('opacity-100');
                    setTimeout(() => {
                        sidebarBackdrop.classList.add('hidden');
                    }, 300);
                }
            };

            openSidebarBtn.addEventListener('click', toggleSidebar);
            closeSidebarBtn.addEventListener('click', toggleSidebar);
            sidebarBackdrop.addEventListener('click', toggleSidebar);

            // Basic Markdown parser for codeblocks
            const parseMarkdown = (text) => {
                text = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

                // Format codeblocks
                text = text.replace(/```(.*?)(\n)([\s\S]*?)```/g, function (match, lang, newline, code) {
                    return `<div class="my-4 rounded-xl overflow-hidden border border-slate-700 bg-slate-950 max-w-full">
                        <div class="bg-slate-800/80 px-4 py-1.5 text-xs text-slate-400 font-mono flex justify-between uppercase w-full">${lang || 'code'}</div>
                        <div class="overflow-x-auto max-w-full custom-scrollbar">
                            <pre class="p-4 text-[13px] leading-relaxed text-slate-300 w-max min-w-full"><code>${code}</code></pre>
                        </div>
                    </div>`;
                });

                // Format inline code
                text = text.replace(/`(.*?)`/g, '<code class="bg-slate-700/60 px-1.5 py-0.5 rounded-md text-pink-300 font-mono text-[13px]">$1</code>');

                // Fix bold
                text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

                // New lines
                text = text.replace(/\n/g, '<br/>');

                return text;
            };

            const renderEmptyState = () => {
                chatHistory.innerHTML = `
                <div class="flex h-full flex-col items-center justify-center text-slate-500 empty-state mt-20">
                    <div class="h-20 w-20 bg-slate-800/50 rounded-full flex items-center justify-center mb-6 shadow-inner ring-1 ring-slate-700/50">
                        <svg class="h-10 w-10 text-indigo-400 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-medium text-slate-300 mb-2">How can I help you today?</h3>
                    <p class="text-sm text-slate-500 max-w-sm text-center">Send a message to start chatting with your local Ollama AI model securely.</p>
                    <div class="mt-8 flex gap-3 text-xs w-full max-w-2xl px-4 justify-center">
                        <button class="bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/50 rounded-xl px-4 py-3 text-slate-400 hover:text-slate-300 text-left transition" onclick="document.getElementById('chatInput').value='Explain quantum computing in simple terms'; document.getElementById('chatInput').focus();">
                            <span class="block text-indigo-400 mb-1">Explain concept...</span>
                            Quantum computing
                        </button>
                        <button class="bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/50 rounded-xl px-4 py-3 text-slate-400 hover:text-slate-300 text-left transition hidden sm:block" onclick="document.getElementById('chatInput').value='Write a Python script to scrape a website'; document.getElementById('chatInput').focus();">
                            <span class="block text-emerald-400 mb-1">Write code...</span>
                            Python web scraper
                        </button>
                    </div>
                </div>`;
            };

            if (currentSessionId) {
                loadSession(currentSessionId);
            } else {
                renderEmptyState();
            }

            chatInput.addEventListener('input', function () {
                this.style.height = 'auto';
                let newHeight = Math.min(this.scrollHeight, 200);
                this.style.height = newHeight + 'px';

                if (this.value.trim() !== '') {
                    sendBtn.classList.remove('opacity-50');
                } else {
                    sendBtn.classList.add('opacity-50');
                }
            });

            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            sendBtn.addEventListener('click', sendMessage);

            newChatBtn.addEventListener('click', async () => {
                const res = await fetch('/api/sessions', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: 'New Chat' })
                });
                const session = await res.json();
                currentSessionId = session.id;

                // Reset active states
                document.querySelectorAll('.session-wrapper').forEach(b => {
                    b.classList.remove('bg-slate-800', 'text-white', 'font-medium', 'active');
                    b.classList.add('hover:bg-slate-800/60', 'text-slate-400');
                    b.querySelector('.session-btn svg')?.classList?.remove('text-indigo-400', 'opacity-100');
                    b.querySelector('.session-btn svg')?.classList?.add('opacity-50', 'group-hover:text-indigo-400');
                });

                const div = document.createElement('div');
                div.className = 'session-wrapper relative group w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm text-white bg-slate-800 font-medium active transition-all';
                div.dataset.id = session.id;
                div.innerHTML = `
                    <button class="session-btn group-2 flex-1 flex items-center gap-3 truncate border-none outline-none bg-transparent">
                        <svg class="w-4 h-4 shrink-0 text-indigo-400 opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span class="truncate text-left w-full pointer-events-none">${session.name}</span>
                    </button>
                    <div class="absolute right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800/80 px-1 rounded-md">
                        <button class="rename-btn p-1 text-slate-400 hover:text-indigo-400" title="Rename"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                        <button class="delete-btn p-1 text-slate-400 hover:text-red-400" title="Delete"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                `;

                div.querySelector('.session-btn').addEventListener('click', () => {
                    swapActiveMenuNode(div);
                    if (window.innerWidth < 768) toggleSidebar();
                    loadSession(session.id);
                });
                attachActionListeners(div);
                sessionList.prepend(div);

                renderEmptyState();
                chatInput.focus();
            });

            function swapActiveMenuNode(activeDiv) {
                document.querySelectorAll('.session-wrapper').forEach(b => {
                    b.classList.remove('bg-slate-800', 'text-white', 'font-medium', 'active');
                    b.classList.add('hover:bg-slate-800/60', 'text-slate-400');
                    b.querySelector('.session-btn svg')?.classList?.remove('text-indigo-400', 'opacity-100');
                    b.querySelector('.session-btn svg')?.classList?.add('opacity-50', 'group-hover:text-indigo-400');
                });
                activeDiv.classList.remove('hover:bg-slate-800/60', 'text-slate-400');
                activeDiv.classList.add('bg-slate-800', 'text-white', 'font-medium', 'active');
                activeDiv.querySelector('.session-btn svg')?.classList?.remove('opacity-50', 'group-hover:text-indigo-400');
                activeDiv.querySelector('.session-btn svg')?.classList?.add('text-indigo-400', 'opacity-100');
            }

            function clearActiveSession() {
                currentSessionId = null;
                document.querySelectorAll('.session-wrapper').forEach(b => b.remove());
                renderEmptyState();
            }

            function attachActionListeners(wrapper) {
                const id = wrapper.dataset.id;

                wrapper.querySelector('.delete-btn').addEventListener('click', async (e) => {
                    e.stopPropagation();
                    if (confirm('Are you sure you want to delete this chat?')) {
                        try {
                            const res = await fetch(`/api/sessions/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken }
                            });
                            if (res.ok) {
                                wrapper.remove();
                                if (currentSessionId == id) {
                                    const nextWrapper = document.querySelector('.session-wrapper');
                                    if (nextWrapper) {
                                        nextWrapper.querySelector('.session-btn').click();
                                    } else {
                                        clearActiveSession();
                                    }
                                }
                            }
                        } catch (err) { console.error('Delete failed:', err); }
                    }
                });

                wrapper.querySelector('.rename-btn').addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const span = wrapper.querySelector('.session-btn span');
                    const currentName = span.textContent;
                    const newName = prompt('Rename this chat:', currentName);

                    if (newName && newName.trim() !== '' && newName !== currentName) {
                        try {
                            const res = await fetch(`/api/sessions/${id}`, {
                                method: 'PUT',
                                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                                body: JSON.stringify({ name: newName.trim() })
                            });
                            if (res.ok) {
                                span.textContent = newName.trim();
                            }
                        } catch (err) { console.error('Rename failed:', err); }
                    }
                });
            }

            document.querySelectorAll('.session-wrapper').forEach(wrapper => {
                wrapper.querySelector('.session-btn').addEventListener('click', () => {
                    swapActiveMenuNode(wrapper);
                    if (window.innerWidth < 768) toggleSidebar();
                    loadSession(wrapper.dataset.id);
                });
                attachActionListeners(wrapper);
            });

            async function loadSession(id) {
                currentSessionId = id;
                chatHistory.innerHTML = '<div class="flex justify-center items-center h-full"><div class="relative w-12 h-12"><div class="absolute inset-0 rounded-full border-t-2 border-indigo-500 animate-spin"></div><div class="absolute inset-2 rounded-full border-r-2 border-purple-500 animate-spin" style="animation-direction: reverse;"></div></div></div>';

                try {
                    const res = await fetch(`/api/sessions/${id}/messages`);
                    const messages = await res.json();

                    chatHistory.innerHTML = '';
                    if (messages.length === 0) {
                        renderEmptyState();
                    } else {
                        messages.forEach(msg => appendMessage(msg.role, msg.content));
                        setTimeout(scrollToBottom, 50);
                    }
                } catch (e) {
                    console.error(e);
                }
                chatInput.focus();
            }

            async function sendMessage() {
                const content = chatInput.value.trim();
                if (!content) return;

                if (!currentSessionId) {
                    newChatBtn.click();
                    setTimeout(() => {
                        chatInput.value = content;
                        sendMessage();
                    }, 400);
                    return;
                }

                const model = document.getElementById('modelSelect').value;

                const emptyState = chatHistory.querySelector('.empty-state');
                if (emptyState) emptyState.remove();

                appendMessage('user', content);
                chatInput.value = '';
                chatInput.style.height = 'auto'; // Reset size
                sendBtn.classList.add('opacity-50');
                scrollToBottom();

                const loadingId = 'loading-' + Date.now();
                appendLoading(loadingId);
                scrollToBottom();

                chatInput.disabled = true;
                sendBtn.disabled = true;

                try {
                    const res = await fetch(`/api/sessions/${currentSessionId}/message`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ content, model })
                    });

                    document.getElementById(loadingId).remove();

                    if (!res.ok) {
                        appendMessage('assistant', `<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-3 rounded-lg flex items-start gap-3"><svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><div>Server Error</div></div>`, true);
                    } else {
                        const contentDiv = appendMessage('assistant', '<div class="flex space-x-1.5 items-center h-6 px-1"><div class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-[bounce_1s_infinite]"></div></div>', true);

                        let fullContent = '';
                        const reader = res.body.getReader();
                        const decoder = new TextDecoder();
                        let buffer = '';
                        let isStreaming = true;

                        while (isStreaming) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            let lines = buffer.split('\n');
                            buffer = lines.pop(); // Keep incomplete line in buffer

                            for (let line of lines) {
                                if (line.startsWith('data: ')) {
                                    try {
                                        const data = JSON.parse(line.substring(6));
                                        if (data.error) {
                                            contentDiv.innerHTML = `<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-3 rounded-lg flex">${data.error}</div>`;
                                        } else if (data.message && data.message.content) {
                                            fullContent += data.message.content;
                                            contentDiv.innerHTML = parseMarkdown(fullContent);
                                            scrollToBottom();
                                        }

                                        if (data.done) {
                                            isStreaming = false;
                                            let activeSpan = document.querySelector(`.session-wrapper[data-id="${currentSessionId}"] .session-btn span`);
                                            if (activeSpan && activeSpan.textContent.trim() === 'New Chat') {
                                                activeSpan.textContent = content.substring(0, 30) + (content.length > 30 ? '...' : '');
                                            }
                                        }
                                    } catch (e) {
                                        console.error('Parse error:', e, line);
                                    }
                                }
                            }
                        }
                    }
                } catch (e) {
                    const l = document.getElementById(loadingId);
                    if (l) l.remove();
                    appendMessage('assistant', `<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-3 rounded-lg flex items-start gap-3"><svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><div>Request failed. Is your local Ollama running?</div></div>`, true);
                }

                chatInput.disabled = false;
                sendBtn.disabled = false;
                chatInput.focus();
                scrollToBottom();
            }

            function appendMessage(role, text, skipParse = false) {
                const template = role === 'user' ? userMsgTemplate.content.cloneNode(true) : aiMsgTemplate.content.cloneNode(true);
                const contentDiv = template.querySelector('.html-content');

                contentDiv.innerHTML = skipParse ? text : (role === 'user' ? text.replace(/\n/g, '<br/>') : parseMarkdown(text));

                if (role === 'assistant') {
                    const btn = template.querySelector('.copy-btn');
                    if (btn) {
                        btn.addEventListener('click', () => {
                            const rawText = contentDiv.innerText;
                            navigator.clipboard.writeText(rawText).then(() => {
                                const originalHTML = btn.innerHTML;
                                btn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>Copied`;
                                btn.classList.add('text-emerald-400');
                                btn.classList.remove('text-slate-400');
                                setTimeout(() => {
                                    btn.innerHTML = originalHTML;
                                    btn.classList.remove('text-emerald-400');
                                    btn.classList.add('text-slate-400');
                                }, 2000);
                            });
                        });
                    }
                }

                chatHistory.appendChild(template);
                return contentDiv;
            }

            function appendLoading(id) {
                const template = aiMsgTemplate.content.cloneNode(true);
                const container = template.querySelector('div.flex.justify-start');
                container.id = id;
                const contentDiv = template.querySelector('.html-content');
                contentDiv.innerHTML = '<div class="flex space-x-1.5 items-center h-6 px-1"><div class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-[bounce_1s_infinite]"></div><div class="w-2.5 h-2.5 rounded-full bg-purple-500 animate-[bounce_1s_infinite] delay-100"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-[bounce_1s_infinite] delay-200"></div></div>';
                chatHistory.appendChild(template);
            }

            function scrollToBottom() {
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }
        });

    </script>
@endsection