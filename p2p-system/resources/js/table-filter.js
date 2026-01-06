/**
 * Client-Side Table Filter Component
 * 
 * Provides real-time filtering, searching, and sorting for tables
 * without server round-trips for better performance.
 * 
 * Usage:
 * <div x-data="tableFilter({{ $data->toJson() }})">
 *   <input type="text" x-model="search" placeholder="Search...">
 *   <select x-model="filters.status">...</select>
 *   <template x-for="item in filteredItems">...</template>
 * </div>
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('tableFilter', (initialData = [], options = {}) => ({
        // Raw data
        items: initialData,

        // Search
        search: '',
        searchDebounceMs: 300,
        searchTimeout: null,

        // Filters
        filters: {
            status: '',
            priority: '',
        },

        // Sorting
        sortColumn: options.defaultSort || '',
        sortDirection: options.defaultDirection || 'asc',

        // Pagination
        currentPage: 1,
        itemsPerPage: options.perPage || 10,

        // Search fields
        searchFields: options.searchFields || ['item_name', 'name'],

        init() {
            // Watch for changes and reset pagination
            this.$watch('search', () => this.currentPage = 1);
            this.$watch('filters', () => this.currentPage = 1, { deep: true });
        },

        // Get filtered and sorted items
        get filteredItems() {
            let result = [...this.items];

            // Apply search filter
            if (this.search.trim()) {
                const searchLower = this.search.toLowerCase();
                result = result.filter(item => {
                    return this.searchFields.some(field => {
                        const value = this.getNestedValue(item, field);
                        return value && String(value).toLowerCase().includes(searchLower);
                    });
                });
            }

            // Apply status filter
            if (this.filters.status) {
                result = result.filter(item => item.status === this.filters.status);
            }

            // Apply priority filter
            if (this.filters.priority) {
                result = result.filter(item => item.priority === this.filters.priority);
            }

            // Apply sorting
            if (this.sortColumn) {
                result.sort((a, b) => {
                    let aVal = this.getNestedValue(a, this.sortColumn);
                    let bVal = this.getNestedValue(b, this.sortColumn);

                    // Handle different types
                    if (typeof aVal === 'string') aVal = aVal.toLowerCase();
                    if (typeof bVal === 'string') bVal = bVal.toLowerCase();

                    // Priority sorting (high > medium > low)
                    if (this.sortColumn === 'priority') {
                        const priorityOrder = { high: 3, medium: 2, low: 1 };
                        aVal = priorityOrder[aVal] || 0;
                        bVal = priorityOrder[bVal] || 0;
                    }

                    let comparison = 0;
                    if (aVal < bVal) comparison = -1;
                    if (aVal > bVal) comparison = 1;

                    return this.sortDirection === 'desc' ? -comparison : comparison;
                });
            }

            return result;
        },

        // Get paginated items
        get paginatedItems() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredItems.slice(start, end);
        },

        // Get total pages
        get totalPages() {
            return Math.ceil(this.filteredItems.length / this.itemsPerPage);
        },

        // Pagination controls
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        nextPage() {
            this.goToPage(this.currentPage + 1);
        },

        prevPage() {
            this.goToPage(this.currentPage - 1);
        },

        // Sorting
        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
        },

        getSortIcon(column) {
            if (this.sortColumn !== column) return '';
            return this.sortDirection === 'asc' ? '↑' : '↓';
        },

        // Clear all filters
        clearFilters() {
            this.search = '';
            this.filters = { status: '', priority: '' };
            this.sortColumn = '';
            this.currentPage = 1;
        },

        // Utility to get nested object values (e.g., 'user.name')
        getNestedValue(obj, path) {
            return path.split('.').reduce((current, key) => current?.[key], obj);
        },

        // Check if any filters are active
        get hasActiveFilters() {
            return this.search || this.filters.status || this.filters.priority;
        },

        // Get count text
        get countText() {
            const total = this.items.length;
            const filtered = this.filteredItems.length;
            if (filtered === total) {
                return `${total} items`;
            }
            return `${filtered} of ${total} items`;
        },
    }));
});
