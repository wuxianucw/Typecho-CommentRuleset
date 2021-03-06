import React from 'react';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';

export default function ListItemLink(props) {
    const { icon, primary, to, ...other } = props;

    const ItemLink = React.useMemo(
        () =>
            React.forwardRef((linkProps, ref) => (
                // eslint-disable-next-line jsx-a11y/anchor-has-content
                <a ref={ref} href={to} {...linkProps} />
            )),
        [to],
    );

    return (
        <ListItem button component={ItemLink} {...other}>
            {icon ? <ListItemIcon>{icon}</ListItemIcon> : null}
            <ListItemText primary={primary} />
        </ListItem>
    );
}
