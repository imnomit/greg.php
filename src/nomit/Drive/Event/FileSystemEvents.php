<?php

namespace nomit\Drive\Event;

final class FileSystemEvents
{

    public const TOUCH_EVENT = 'fs.touch';

    public const SET_OWNER_EVENT = 'fs.set_owner';

    public const SET_GROUP_EVENT = 'fs.set_group';

    public const SET_MODE_EVENT = 'fs.set_mode';

    public const BEFORE_DELETE_EVENT = 'fs.delete.before';

    public const DELETE_EVENT = 'fs.delete.delete';

    public const COPY_EVENT = 'fs.copy';

    public const MOVE_EVENT = 'fs.move';

    public const CREATE_DIRECTORY_EVENT = 'fs.directory.create';

    public const CREATE_FILE_EVENT = 'fs.file.create';

    public const WRITE_EVENT = 'fs.write';

    public const APPEND_EVENT = 'fs.append';

    public const TRUNCATE_EVENT = 'fs.truncate';

}