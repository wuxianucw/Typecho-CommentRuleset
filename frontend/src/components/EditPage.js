import React from 'react';

export default function EditPage(props) {
    const { match } = props;

    return (
        <div>EditPage{match.params.ruid && `(RUID=${match.params.ruid})`}</div>
    );
}
