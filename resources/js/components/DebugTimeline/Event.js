import upperFirst from 'lodash/upperFirst';

class Event {
    constructor({
        microtime,
        type,
        label,
        metadata = null,
        context = null,
        file = null,
        line_number = null,
    }) {
        this.microtime = microtime;
        this.type = type;
        this.label = label;
        this.metadata = metadata;
        this.context = context;
        this.file = file;
        this.line_number = line_number;
    }

    static forQuery({ microtime, sql, time, connection_name, bindings }) {
        return new Event({
            microtime,
            type: 'query',
            label: sql,
            metadata: {
                time,
                connection_name,
            },
            context: bindings,
        });
    }

    static forDump({ microtime, html_dump, file, line_number }) {
        return new Event({
            microtime,
            type: 'dump',
            label: html_dump,
            file,
            line_number,
        });
    }

    static forLog({ microtime, context, level, message }) {
        return new Event({
            microtime,
            type: 'log',
            label: message,
            context,
            metadata: { level },
        });
    }

    static forGlow({ microtime, message_level, meta_data, name, time }) {
        return new Event({
            microtime,
            type: 'glow',
            label: name,
            context: meta_data,
            metadata: { time, message_level },
        });
    }

    getComponentName() {
        return upperFirst(this.type) + 'Event';
    }
}

export default Event;
