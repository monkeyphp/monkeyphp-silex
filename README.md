Monkeyphp
=========

Install php dependencies
    
    php composer.phar install


    gem install --binstubs

    bundle install --binstubs

    cd tools/chef

    bundle exec librarian-chef install

    cd ../vagrant

    vagrant up

Varnish tools
https://www.varnish-cache.org/docs/3.0/reference/varnishadm.html

    varnishadm

    ban.url .

    service varnish restart

Elasticsearch

    curl -XGET 'http://localhost:9200/monkeyphp/user/_search?pretty=true' -d '{"query":{"term":{"username": "monkeyphp"}}}'

