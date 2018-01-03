src = client/src/buy.go

all: public/clients/algolia-keys public/clients/algolia-keys-mac

public/clients/algolia-keys: $(src)
	GOOS=linux GOARCH=amd64 go build $(src)
	mv buy public/clients/algolia-keys

public/clients/algolia-keys-mac: $(src)
	GOOS=darwin GOARCH=amd64 go build $(src)
	mv buy public/clients/algolia-keys-mac
