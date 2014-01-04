Monkeyphp
=========

Silex application for monkeyphp.com

Install php dependencies
    
    php composer.phar install

    gem install bundler

    bundle install --binstubs

    cd tools/chef

    bundle exec librarian-chef install

    cd ../vagrant

    vagrant up

Varnish 
=======

http://kly.no/varnish/regex.txt

Tools
-----
https://www.varnish-cache.org/docs/3.0/reference/varnishadm.html

    varnishadm

    ban.url .

    service varnish restart

Elasticsearch

    curl -XGET 'http://localhost:9200/monkeyphp/user/_search?pretty=true' -d '{"query":{"term":{"username": "monkeyphp"}}}'

Delete an index
    curl -XDELETE http://localhost:9200/monkeyphp

Delete a mapping
    curl -XDELETE http://localhost:9200/monkeyphp/article/_mapping

Print current article mapping 
    curl -XGET http://localhost:9200/monkeyphp/article/_mapping?pretty=true

Print out various resultsets

    curl -XGET 'http://localhost:9200/monkeyphp/category/_search?q=*:*&pretty=true'

    curl -XGET 'http://localhost:9200/monkeyphp/article/_search?q=*:*&pretty=true'

    curl -XGET 'http://localhost:9200/monkeyphp/comment/_search?q=*:*&pretty=true'

