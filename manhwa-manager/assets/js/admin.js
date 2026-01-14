/**
 * Manhwa Manager Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // File Upload Handler
        initFileUpload();
        
        // Drag and Drop
        initDragDrop();
        
        // JSON Preview
        initJSONPreview();
        
        // Chapter Management
        initChapterManagement();
        
        // Bulk Actions
        initBulkActions();
    });
    
    /**
     * Initialize File Upload
     */
    function initFileUpload() {
        $('#manhwa_json_file').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                validateJSONFile(file);
            }
        });
    }
    
    /**
     * Initialize Drag and Drop
     */
    function initDragDrop() {
        const uploadArea = $('.manhwa-upload-area');
        
        if (uploadArea.length === 0) return;
        
        uploadArea.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        uploadArea.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        uploadArea.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'application/json') {
                    $('#manhwa_json_file')[0].files = files;
                    validateJSONFile(file);
                } else {
                    showNotification('Please upload a JSON file', 'error');
                }
            }
        });
    }
    
    /**
     * Validate JSON File
     */
    function validateJSONFile(file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                const json = JSON.parse(e.target.result);
                
                // Validate structure
                if (!json.manhwa || !Array.isArray(json.manhwa)) {
                    showNotification('Invalid JSON structure. Missing "manhwa" array.', 'error');
                    return;
                }
                
                // Show preview
                showJSONPreview(json);
                showNotification(`Valid JSON file with ${json.manhwa.length} manhwa entries`, 'success');
                
            } catch (error) {
                showNotification('Invalid JSON file: ' + error.message, 'error');
            }
        };
        
        reader.readAsText(file);
    }
    
    /**
     * Show JSON Preview
     */
    function showJSONPreview(json) {
        const previewContainer = $('#json-preview-container');
        
        if (previewContainer.length === 0) {
            $('<div id="json-preview-container" class="json-preview" style="margin-top: 20px;"></div>')
                .insertAfter('.manhwa-upload-area');
        }
        
        const preview = JSON.stringify(json, null, 2);
        $('#json-preview-container').html('<pre>' + escapeHtml(preview) + '</pre>');
    }
    
    /**
     * Initialize JSON Preview
     */
    function initJSONPreview() {
        // Add preview button for existing files
        $('.json-file-input').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const json = JSON.parse(e.target.result);
                        showJSONPreview(json);
                    } catch (error) {
                        console.error('JSON parse error:', error);
                    }
                };
                reader.readAsText(file);
            }
        });
    }
    
    /**
     * Initialize Chapter Management
     */
    function initChapterManagement() {
        let chapterIndex = $('.chapter-item').length;
        
        // Add Chapter Button
        $(document).on('click', '#add-chapter', function(e) {
            e.preventDefault();
            
            const chapterHtml = `
                <div class="chapter-item" style="background: #f5f5f5; padding: 15px; margin-bottom: 10px; border-radius: 8px; border-left: 4px solid #667eea;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 100px; gap: 10px; align-items: center;">
                        <div>
                            <label><strong>Chapter Title:</strong></label>
                            <input type="text" name="manhwa_chapters[${chapterIndex}][title]" placeholder="e.g., Chapter 1: The Beginning" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label><strong>Chapter URL:</strong></label>
                            <input type="text" name="manhwa_chapters[${chapterIndex}][url]" placeholder="https://example.com/chapter-1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <button type="button" class="button remove-chapter" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">Remove</button>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <label><strong>Release Date:</strong></label>
                        <input type="date" name="manhwa_chapters[${chapterIndex}][date]" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </div>
            `;
            
            $('#chapters-list').append(chapterHtml);
            chapterIndex++;
            
            // Animate new chapter
            $('.chapter-item:last').hide().fadeIn(300);
        });
        
        // Remove Chapter Button
        $(document).on('click', '.remove-chapter', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to remove this chapter?')) {
                $(this).closest('.chapter-item').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
        
        // Sort chapters
        if (typeof $.fn.sortable !== 'undefined') {
            $('#chapters-list').sortable({
                handle: '.chapter-item',
                placeholder: 'chapter-placeholder',
                opacity: 0.6
            });
        }
    }
    
    /**
     * Initialize Bulk Actions
     */
    function initBulkActions() {
        // Select all checkbox
        $('#select-all-manhwa').on('change', function() {
            $('.manhwa-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Bulk delete
        $('#bulk-delete').on('click', function(e) {
            e.preventDefault();
            
            const selected = $('.manhwa-checkbox:checked');
            if (selected.length === 0) {
                showNotification('Please select at least one manhwa', 'warning');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${selected.length} manhwa?`)) {
                // Perform bulk delete via AJAX
                bulkDelete(selected);
            }
        });
    }
    
    /**
     * Bulk Delete via AJAX
     */
    function bulkDelete(selected) {
        const ids = [];
        selected.each(function() {
            ids.push($(this).val());
        });
        
        $.ajax({
            url: manhwaManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'manhwa_bulk_delete',
                nonce: manhwaManager.nonce,
                ids: ids
            },
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showNotification(response.data.message, 'success');
                    location.reload();
                } else {
                    showNotification(response.data.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('An error occurred', 'error');
            }
        });
    }
    
    /**
     * Show Notification
     */
    function showNotification(message, type) {
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        
        const notification = $(`
            <div class="manhwa-notice ${type}" style="position: fixed; top: 50px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.3s ease;">
                <span style="font-size: 24px;">${icons[type] || 'ℹ'}</span>
                <span>${message}</span>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Show Loading
     */
    function showLoading() {
        const loader = $(`
            <div id="manhwa-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center;">
                <div class="manhwa-spinner"></div>
            </div>
        `);
        $('body').append(loader);
    }
    
    /**
     * Hide Loading
     */
    function hideLoading() {
        $('#manhwa-loader').remove();
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    /**
     * Format File Size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .chapter-placeholder {
            background: #e0e0e0;
            border: 2px dashed #999;
            height: 100px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
    `;
    document.head.appendChild(style);
    
})(jQuery);
