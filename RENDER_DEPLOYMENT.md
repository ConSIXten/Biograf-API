# Deploy Biograf API to Render

## Prerequisites
1. GitHub account
2. Render account (free): https://render.com
3. Database (choose one):
   - PlanetScale (free): https://planetscale.com
   - Railway MySQL ($5/month): https://railway.app

## Step-by-Step Deployment

### Step 1: Prepare Database (PlanetScale - FREE)

1. Go to https://planetscale.com and sign up
2. Create new database: `biograf_db`
3. Click "Connect" → Get connection string
4. Import your database:
   - Export from phpMyAdmin: `biograf_db` → Export → Go
   - Use PlanetScale CLI or phpMyAdmin to import

### Step 2: Push Code to GitHub

```bash
cd /Applications/MAMP/htdocs/biograf-api

# Initialize git
git init

# Create .gitignore
echo "AIRCRAFT-API/" >> .gitignore
echo ".DS_Store" >> .gitignore
echo "*.log" >> .gitignore

# Add all files
git add .

# Commit
git commit -m "Initial commit - Biograf API"

# Create repo on GitHub (go to github.com/new)
# Then link it:
git remote add origin https://github.com/YOUR_USERNAME/biograf-api.git
git branch -M main
git push -u origin main
```

### Step 3: Deploy on Render

1. Go to https://render.com/dashboard
2. Click "New +" → "Web Service"
3. Connect your GitHub repository
4. Configure:
   - **Name**: `biograf-api`
   - **Runtime**: `Docker`
   - **Branch**: `main`
   - **Instance Type**: Free

5. **Add Environment Variables** (IMPORTANT!):
   Click "Advanced" → Add Environment Variables:
   ```
   DB_HOST=your-planetscale-host.psdb.cloud
   DB_NAME=biograf_db
   DB_USER=your-username
   DB_PASSWORD=your-password
   DB_PORT=3306
   ```

6. Click "Create Web Service"

### Step 4: Wait for Deployment

Render will:
- Build your Docker image
- Deploy your API
- Give you a URL like: `https://biograf-api.onrender.com`

### Step 5: Update Frontend

In your Render frontend service:
- Go to Environment
- Add/Update: `VITE_API_URL=https://biograf-api.onrender.com/api`
- Redeploy

### Step 6: Test Your API

```
https://biograf-api.onrender.com/api/cinemas/read.php
https://biograf-api.onrender.com/api/showtimes/read.php
```

## Alternative: Railway (Easier but Costs $5/month)

Railway includes MySQL database and is simpler:

1. Go to https://railway.app
2. "New Project" → "Deploy from GitHub repo"
3. Select your repo
4. Railway auto-detects PHP
5. Add MySQL database (included)
6. Set environment variables automatically
7. Done!

## Troubleshooting

### Database Connection Failed
- Check environment variables are set correctly
- Verify database host/credentials
- Check PlanetScale connection is allowed

### CORS Errors
- Already fixed in the code with headers
- Should work out of the box

### API Returns 500 Error
- Check Render logs: Dashboard → Your Service → Logs
- Usually database connection issue

## Free vs Paid Hosting

| Service | Database | Cost | Best For |
|---------|----------|------|----------|
| Render + PlanetScale | MySQL (Free) | FREE | Testing/Learning |
| Railway | MySQL (Included) | $5/mo | Production |
| Hostinger | MySQL (Included) | $3/mo | Production |

## My Recommendation

**For production**: Use **Railway** ($5/month) - includes everything, super easy

**For testing/portfolio**: Use **Render + PlanetScale** (FREE)

---

Need help? Let me know which option you choose!
