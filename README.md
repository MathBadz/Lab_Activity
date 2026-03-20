# ITSAR2 313 - System Integration and Architecture 2

## Group Members

- Badajos, Math Auric Ros
- Salazar, Shirley Ann
- Villalobos, Jon Nathaniel
- Yanson, Rea Nicole
- Zambra, Maika

## Repository Instructions

This repository is organized by lab branches. Use the commands below from the project root.

### 1. Check current branches

```powershell
git branch --all
```

### 2. Switch to a lab branch

```powershell
git checkout lab1
git checkout lab2
git checkout lab3
```

### 3. If a lab branch does not exist yet, create it

```powershell
git checkout -b lab1
git push -u origin lab1

git checkout -b lab2
git push -u origin lab2

git checkout -b lab3
git push -u origin lab3
```

### 4. Recommended workflow per lab branch

```powershell
git checkout lab1   # or lab2 / lab3
git pull
# make your changes
git add .
git commit -m "Lab update"
git push
```

## Notes

- Keep each laboratory activity in its corresponding branch (`lab1`, `lab2`, `lab3`).
- Use clear commit messages so contributions are easy to track.
- Merge branches to `master` only when the lab output is complete and reviewed.
