# Image Bot
Bot that (re)schedules, resizes and store images into cloud storage.

The workflow should be divided into the following independent steps:
- Schedule list of images to be processed.
- Resize scheduled images.
- Upload resized image to cloud storage.

## Installation

#### Via composer
Run:
```
$ php composer require mike-jc/image-bot
```
After that you can use any other bot command you want:
```
$ php vendor/bin schedule ./images
$ php vendor/bin resize -n 10
$ php vendor/bin upload
```

#### Or as a Docker image
Run the following commands:
```
$ mkdir sandbox && cd sandbox
$ curl -sS https://raw.githubusercontent.com/mike-jc/ImageBot/master/Dockerfile > Dockerfile 
$ docker build -t bots/image-bot .
$ docker run -it --volume=<absolute-path-to-images-directory>:/images --workdir="/images" --entrypoint=/bin/bash bots/image-bot
```
After that Docker container will run and you will get into it.

In the running docker container:
```
$ vim /home/ImageBot/config/config.yml

... edit configuration (at least, add cloud storage credentials)

$ /etc/init.d/rabbitmq-server start

... now you're in images directory, so:

$ bot schedule ./ 
$ bot resize

... use any other bot command you want
```

## Configuration
Configuration is placed in `config/config.yml` and has to be written in YAML format.

You need at least to choose cloud storage and add credentials for it.
Currently the following cloud storages are supported:
- Amazon S3
- Dropbox
- Google Drive

#### Configuration for Amazon S3
```
    type: amazon-s3
    parameters:
        region: YOUR_REGION_IN_AMAZON
        bucket-name: YOUR_BUCKET_NAME # by default: `default-bucket`
    credentials:
        key: YOU_CLIENT_KEY
        sercret: YOU_CLIENT_SECRET
```

#### Configuration for Dropbox
```
storage:
    type: dropbox # can be: amazon-s3 for Amazon S3 storage, dropbox for Dropbox, g-drive for Google Drive
    credentials: # different structure for each storage type
        key: YOUR_CLIENT_KEY
        secret: YOUR_CLIENT_SECRET
        access-token: YOUR_DEVELOPER_ACCESS_TOKEN
```

#### Configuration for Google Drive
```
    type: g-drive
    credentials:
        config-file: PATH_TO_FILE  # file with credentials in JSON for service account (not for web client!)
```

#### Other configurable parameters
```
queues:
    host: localhost     # connection parameters for RabbitMQ
    port: 5672
    user: admin
    password: admin
    names:              # names of queues in RabbitMQ
        resizer: resize
        storage: upload
        success: done
        failure: failed

resizer:
    side: 640           # size to which images should be resized
    bg-color: '#ffffff' # what colour to use for background if image does not fit new ratio
    delete-origin: true # delete origin images after successful resized or not
```
All settings above are optional and can be omitted in your `config.yml` file.

## Usage

#### Schedule list of images
Use `schedule` command that accepts a path to the directory with images and schedule them for resize:
```
$ bot schedule ./images
```
#### Resize scheduled images
Use `resize` command that accept option `-n <count>`.
Takes next *count* of images from resize queue and resizes them to 640x640 pixels
in JPEG format. If image is not a square shape resizer should make it square by means of
adding a white background.

The next command will resize not more then 12 images:
```
$ bot resize -n 12
```
To resize all images in the queue:
```
$ bot resize
```
Resized images will be stored in directory called `<origin directory>_resized`. That is new directory will be created (if not exists) with name of origin directory where images were placed plus suffix `_resized`.
If resize goes well original image will be removed from origin directory (this behaviour can be changed in configuration).

#### Upload resized images to the storage
Use `upload` command that accept option `-n <count>`.
Uploads next count of images from upload queue to one of the remote storages. Type of
cloud storage and corresponding credentials should be set in config file. There can be only
one remote storage at the moment.

The next command will upload not more then 5 images:
```
$ bot upload -n 5
```
To upload all images in the queue:
```
$ bot upload
```

#### Monitoring
You can get status of all steps in the process using command `status`.
It will output all queues with a count of images in them:
```
$ bot status
Queue   Count
resize  0       # origin images scheduled for resizing
upload  12      # resized images scheduled for uploading 
done    42      # uploaded images
failed  4       # images which could not be resized/uploaded for some reason
```

#### Rescheduling "failed" images
Sometimes images could not be uploaded (e.g. due to network problems) or resized.
Use `retry` command that accept option `-n <count>`.
It moves all (or the chosen count) "failed" images again to the queue for resizing.

The next command will reschedule not more then 3 images:
```
$ bot retry -n 3
```
To retry all images in the queue:
```
$ bot retry
```
