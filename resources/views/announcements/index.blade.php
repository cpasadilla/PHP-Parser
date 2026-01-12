<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            {{ __('Announcements - Freedom Wall') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Display success message if announcement was just posted -->
            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif

            <!-- Main Grid Layout: Feed on Left, Form on Right -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Left Column: Recent Announcements Feed -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Recent Announcements
                            <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                (<span id="announcementCount">{{ count($announcements) }}</span>)
                            </span>
                        </h3>

                        <div id="announcementsList" class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse($announcements as $announcement)
                                <div class="announcement-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition" data-announcement-id="{{ $announcement->id }}">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            @if($announcement->title)
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $announcement->title }}
                                                </h4>
                                            @endif
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                by <strong>{{ trim(($announcement->user->fName ?? '') . ' ' . ($announcement->user->lName ?? '')) ?: 'Unknown User' }}</strong>
                                                on {{ $announcement->created_at->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                        @if(Auth::id() === $announcement->user_id || Auth::user()->roles && in_array(strtoupper(trim(Auth::user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR']))
                                            <button 
                                                onclick="deleteAnnouncement({{ $announcement->id }})"
                                                class="ml-4 px-3 py-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded hover:bg-red-100 dark:hover:bg-red-900/40 transition"
                                            >
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words text-sm">
                                        {{ $announcement->content }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <p class="text-gray-500 dark:text-gray-400 mb-2">
                                        No announcements yet. Be the first to post one!
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Post an Announcement Form -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Post an Announcement</h3>
                        
                        <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Title (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    placeholder="Enter announcement title..." 
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    maxlength="255"
                                />
                            </div>

                            <div class="mb-4">
                                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="content" 
                                    name="content" 
                                    placeholder="Share your announcement here..." 
                                    rows="5"
                                    required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-vertical"
                                    maxlength="5000"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span id="charCount">0</span> / 5000 characters
                                </p>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button 
                                    type="button" 
                                    onclick="document.getElementById('content').value = ''; document.getElementById('title').value = '';"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                >
                                    Clear
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    id="submitBtn"
                                >
                                    Post Announcement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Character counter
        document.getElementById('content').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Form submission with AJAX
        document.getElementById('announcementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';

            const formData = new FormData(this);

            fetch('{{ route("announcements.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Clear the form
                    document.getElementById('announcementForm').reset();
                    document.getElementById('charCount').textContent = '0';
                    
                    // Show success message
                    showNotification('Announcement posted successfully!', 'success');
                    
                    // Reload announcements
                    loadAnnouncements();
                } else {
                    showNotification(data.message || 'Error posting announcement', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error posting announcement. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });

        // Load announcements via AJAX
        function loadAnnouncements() {
            fetch('{{ route("announcements.get") }}')
            .then(response => response.json())
            .then(data => {
                const announcementsList = document.getElementById('announcementsList');
                const announcementCount = document.getElementById('announcementCount');
                
                if (data.length === 0) {
                    announcementsList.innerHTML = `
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 mb-2">
                                No announcements yet. Be the first to post one!
                            </p>
                        </div>
                    `;
                    announcementCount.textContent = '0';
                    return;
                }

                announcementsList.innerHTML = data.map(announcement => {
                    const isOwner = {{ Auth::id() }} === announcement.user_id;
                    const isAdmin = {{ Auth::user()->roles && in_array(strtoupper(trim(Auth::user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR']) ? 'true' : 'false' }};
                    const canDelete = isOwner || isAdmin;
                    
                    let html = `
                        <div class="announcement-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition" data-announcement-id="${announcement.id}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                    `;
                    
                    if (announcement.title) {
                        html += `<h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">${escapeHtml(announcement.title)}</h4>`;
                    }
                    
                    html += `
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        by <strong>${escapeHtml(announcement.user_name)}</strong>
                                        on ${announcement.created_at}
                                    </p>
                                </div>
                    `;
                    
                    if (canDelete) {
                        html += `
                            <button 
                                onclick="deleteAnnouncement(${announcement.id})"
                                class="ml-4 px-3 py-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded hover:bg-red-100 dark:hover:bg-red-900/40 transition"
                            >
                                Delete
                            </button>
                        `;
                    }
                    
                    html += `
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words">
                                ${escapeHtml(announcement.content)}
                            </div>
                        </div>
                    `;
                    
                    return html;
                }).join('');
                
                announcementCount.textContent = data.length;
            })
            .catch(error => console.error('Error loading announcements:', error));
        }

        // Delete announcement
        function deleteAnnouncement(announcementId) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                fetch(`/announcements/${announcementId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Announcement deleted successfully!', 'success');
                        loadAnnouncements();
                    } else {
                        showNotification(data.message || 'Error deleting announcement', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error deleting announcement', 'error');
                });
            }
        }

        // Show notification
        function showNotification(message, type) {
            const alertClass = type === 'success' 
                ? 'bg-green-100 border-green-400 text-green-700' 
                : 'bg-red-100 border-red-400 text-red-700';
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 border rounded z-50 ${alertClass}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 4000);
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Reload announcements every 5 seconds for real-time updates
        setInterval(loadAnnouncements, 5000);
    </script>
</x-app-layout>
