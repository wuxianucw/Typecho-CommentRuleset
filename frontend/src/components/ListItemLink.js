import React from 'react';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';

export default function ListItemLink(props) {
    const { icon, primary, to } = props;

    const ItemLink = React.useMemo(
        () =>
            React.forwardRef((linkProps, ref) => (
                <a ref={ref} href={to} {...linkProps} />
            )),
        [to],
    );

    // eslint-disable-next-line
    return (
        <li>
            <ListItem button component={ItemLink}>
                {icon ? <ListItemIcon>{icon}</ListItemIcon> : null}
                <ListItemText primary={primary} />
            </ListItem>
        </li>
    );
}
