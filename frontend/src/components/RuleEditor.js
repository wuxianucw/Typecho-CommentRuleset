import React from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import Button from '@material-ui/core/Button';
import { makeStyles, useTheme } from '@material-ui/core/styles';

export default function RuleEditor(props) {
    const theme = useTheme();

    const [editMode, setEditMode] = React.useState(0); // 0 => 所见即所得编辑模式，1 => 规则文本编辑模式

    const handleSwitchMode = () => {
        setEditMode((mode) => [1, 0][mode]);
    };

    return (
        <>
            <InputLabel required shrink>规则内容</InputLabel>
            <Button
                variant="contained"
                color={["primary", "secondary"][editMode]}
                onClick={handleSwitchMode}
                style={{ marginTop: theme.spacing(1) }}
            >切换到{["规则文本", "所见即所得"][editMode]}编辑模式</Button>
        </>
    );
}
