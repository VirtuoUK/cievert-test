#Install dependencies
```
 docker run --rm -it --volume ${PWD}:/app composer install
```

# Build & Tag
```
 docker build -t cievert
``` 

# Run
```
docker run --rm -v ${pwd}:/app cievert app:check google.com -t Google -s 200
```