# Performance Optimization Guide

## ✅ Implemented Optimizations

### 1. Livewire Real-time (Already Done)
- All filters use `wire:model.live` - NO page reloads
- Search uses debounce (300ms) to prevent excessive requests
- Sorting via `wire:click` - instant, no page reload

### 2. Lazy Loading Prevention
- `Model::preventLazyLoading()` enabled in development
- Catches N+1 query issues before they reach production
- Logs warning when lazy loading detected

### 3. Slow Query Logging
- Queries taking >500ms are logged as warnings
- Check `storage/logs/laravel.log` for slow queries
- Helps identify bottlenecks

### 4. Eager Loading (Already Optimized)
- OEE Report: `with('equipment:id,name,serial_number,sublocation_id')`
- SLA Report: Uses optimized raw SQL with JOINs
- Calendar: `with(['equipment:id,name', 'maintCategory:id,name'])`
- Dashboard: `with(['equipment:id,name', 'assignedUser:id,name'])`

---

## 🚀 Production Deployment Commands

Run these commands before deploying to production:

```bash
# Cache configuration (merge all config files into one)
php artisan config:cache

# Cache routes (speeds up route resolution)
php artisan route:cache

# Cache views (pre-compile all Blade templates)
php artisan view:cache

# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev

# Build assets for production
npm run build
```

---

## 📊 Performance Tips for Reports

1. **Use Indexes** - Key columns like `year`, `month`, `equipment_id`, `status` have indexes
2. **Select Specific Columns** - Never use `SELECT *`, always specify needed columns
3. **Raw Queries for Aggregations** - SLA/SLM use raw SQL for complex aggregations
4. **Limit Results** - Dashboard limits recent tasks to 10 items
