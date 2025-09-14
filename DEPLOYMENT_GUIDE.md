# CORS Configuration & InfinityFree Deployment Guide

## Overview
This guide explains how to deploy your Laravel Hardware Repair Management System to InfinityFree hosting with proper CORS configuration.

## CORS Configuration

The system has been configured with environment-specific CORS settings:

### Local Development
- Allows all origins (`*`) for easy development
- Full CORS support for all paths
- Credentials support enabled

### Production Environment
- Restricts origins to your specific domain
- Enhanced security with specific headers
- Optimized for InfinityFree hosting

## InfinityFree Deployment Steps

### 1. Environment Configuration

Create a `.env` file in your production environment with these settings:

```env
APP_NAME="Hardware Repair System"
APP_ENV=production
APP_KEY=your_generated_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.infinityfreeapp.com

# Database Configuration (use InfinityFree provided details)
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# CORS Configuration for Production
CORS_MAX_AGE=86400

# Other configurations...
```

### 2. CORS Settings Explanation

The CORS configuration automatically adapts based on your environment:

**Local Environment (`APP_ENV=local`):**
- `allowed_origins`: `['*']` - Allows all origins
- Perfect for development with hot reloading

**Production Environment (`APP_ENV=production`):**
- `allowed_origins`: Uses your `APP_URL` and its variations
- Automatically includes HTTP and HTTPS versions
- More secure and specific to your domain

### 3. File Upload to InfinityFree

1. Upload all files to your `htdocs` folder
2. Ensure the `public` folder contents are in the root `htdocs`
3. Move the Laravel application files to a subfolder (e.g., `laravel`)
4. Update the `index.php` file paths accordingly

### 4. Additional InfinityFree Considerations

#### .htaccess Configuration
Ensure your `.htaccess` file includes CORS headers:

```apache
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "https://yourdomain.infinityfreeapp.com"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-Inertia, X-Inertia-Version"
    Header always set Access-Control-Allow-Credentials "true"
    Header always set Access-Control-Max-Age "86400"
</IfModule>

# Handle preflight OPTIONS requests
RewriteEngine On
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]
```

#### Database Migration
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Testing CORS Configuration

After deployment, test CORS functionality:

1. Open browser developer tools
2. Navigate to your application
3. Check for CORS errors in the console
4. Test API endpoints from different origins (if applicable)

### 6. Troubleshooting Common CORS Issues

#### Issue: "Access-Control-Allow-Origin" error
**Solution:** Verify your `APP_URL` in the `.env` file matches your domain exactly.

#### Issue: Credentials not allowed
**Solution:** Ensure `supports_credentials` is set to `true` in `config/cors.php`.

#### Issue: Preflight requests failing
**Solution:** Add the `.htaccess` rules mentioned above to handle OPTIONS requests.

### 7. Security Best Practices

1. **Never use `['*']` for `allowed_origins` in production**
2. **Always specify your exact domain in production**
3. **Use HTTPS in production** (`APP_URL` should start with `https://`)
4. **Regularly update your CORS configuration** as your application grows

### 8. Environment Variables for CORS

Optional environment variables you can set:

```env
# Custom CORS pattern (if needed)
CORS_ALLOWED_ORIGINS_PATTERN=

# Cache duration for preflight requests
CORS_MAX_AGE=86400
```

## Files Modified for CORS Support

1. **`config/cors.php`** - Main CORS configuration
2. **`bootstrap/app.php`** - CORS middleware registration
3. **`.env.example`** - Environment variable examples

## Support

If you encounter CORS issues:
1. Check browser developer console for specific error messages
2. Verify your `.env` configuration
3. Ensure your domain matches exactly in `APP_URL`
4. Test with browser extensions disabled

Your Laravel application is now CORS-ready for both local development and InfinityFree production deployment!