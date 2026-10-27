#!/bin/bash
echo "Starting FinSight AI..."

# Ensure clean state and build frontend assets
rm -f public/hot
if [ ! -f public/build/manifest.json ]; then
    echo "Building frontend assets..."
    npm run build
fi

# Start Laravel backend in the background
echo "Starting Laravel Backend..."
> storage/logs/server.log
php artisan serve > storage/logs/server.log 2>&1 &
SERVER_PID=$!

# Start Vite frontend in the background
echo "Starting Vite Frontend..."
npm run dev > storage/logs/vite.log 2>&1 &
VITE_PID=$!

echo "Both services are starting up..."

# Detect actual backend port
APP_URL="http://127.0.0.1:8000"
for i in {1..20}; do
    DETECTED_URL=$(grep -o 'http://127\.0\.0\.1:[0-9]\+' storage/logs/server.log 2>/dev/null | tail -n 1)
    if [ -n "$DETECTED_URL" ]; then
        APP_URL="$DETECTED_URL"
        break
    fi
    sleep 0.5
done

echo "Opening browser at $APP_URL..."

# Open the browser
if command -v xdg-open &> /dev/null; then
    xdg-open "$APP_URL"
elif command -v google-chrome &> /dev/null; then
    google-chrome "$APP_URL"
elif command -v firefox &> /dev/null; then
    firefox "$APP_URL"
fi

echo "------------------------------------------------"
echo "FinSight AI is running at $APP_URL"
echo "Press Ctrl+C to stop all services."
echo "------------------------------------------------"

# Wait for user to stop the script
trap "kill $SERVER_PID $VITE_PID 2>/dev/null; rm -f public/hot; exit" INT TERM EXIT
wait
