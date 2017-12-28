all: public/clients/algolia-keys
	
public/clients/algolia-keys: client/src/buy.go
	GOOS=linux GOARCH=amd64 go build client/src/buy.go
	mv buy public/clients/algolia-keys
