import { render } from '@wordpress/element';
import { TabPanel, Panel, PanelBody, TextControl, ColorPicker, SelectControl, TextareaControl, Button, Notice } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import './index.css';

function App() {
    const [settings, setSettings] = useState({
        general: {
            site_title: '',
            admin_email: ''
        },
        appearance: {
            admin_color: '#ffffff',
            menu_position: 'left'
        },
        advanced: {
            custom_css: '',
            custom_js: ''
        }
    });
    const [notice, setNotice] = useState(null);

    useEffect(() => {
        apiFetch({ path: '/wp/v2/settings/wrp_settings' }).then((response) => {
            if (response) {
                setSettings(response);
            }
        });
    }, []);

    const saveSettings = () => {
        apiFetch({
            path: '/wp/v2/settings/wrp_settings',
            method: 'POST',
            data: settings
        }).then(() => {
            setNotice({ status: 'success', message: __('Settings saved successfully!', 'wp-react-admin-panel') });
        }).catch((error) => {
            setNotice({ status: 'error', message: error.message });
        });
    };

    const updateSettings = (section, key, value) => {
        setSettings(prev => ({
            ...prev,
            [section]: {
                ...prev[section],
                [key]: value
            }
        }));
    };

    const tabs = [
        {
            name: 'general',
            title: __('General', 'wp-react-admin-panel'),
            className: 'tab-general',
            content: (
                <PanelBody>
                    <TextControl
                        label={__('Site Title', 'wp-react-admin-panel')}
                        value={settings.general.site_title}
                        onChange={(value) => updateSettings('general', 'site_title', value)}
                    />
                    <TextControl
                        label={__('Admin Email', 'wp-react-admin-panel')}
                        type="email"
                        value={settings.general.admin_email}
                        onChange={(value) => updateSettings('general', 'admin_email', value)}
                    />
                </PanelBody>
            )
        },
        {
            name: 'appearance',
            title: __('Appearance', 'wp-react-admin-panel'),
            className: 'tab-appearance',
            content: (
                <PanelBody>
                    <ColorPicker
                        label={__('Admin Color', 'wp-react-admin-panel')}
                        color={settings.appearance.admin_color}
                        onChangeComplete={(value) => updateSettings('appearance', 'admin_color', value.hex)}
                    />
                    <SelectControl
                        label={__('Menu Position', 'wp-react-admin-panel')}
                        value={settings.appearance.menu_position}
                        options={[
                            { label: __('Left', 'wp-react-admin-panel'), value: 'left' },
                            { label: __('Right', 'wp-react-admin-panel'), value: 'right' }
                        ]}
                        onChange={(value) => updateSettings('appearance', 'menu_position', value)}
                    />
                </PanelBody>
            )
        },
        {
            name: 'advanced',
            title: __('Advanced', 'wp-react-admin-panel'),
            className: 'tab-advanced',
            content: (
                <PanelBody>
                    <TextareaControl
                        label={__('Custom CSS', 'wp-react-admin-panel')}
                        value={settings.advanced.custom_css}
                        onChange={(value) => updateSettings('advanced', 'custom_css', value)}
                    />
                    <TextareaControl
                        label={__('Custom JavaScript', 'wp-react-admin-panel')}
                        value={settings.advanced.custom_js}
                        onChange={(value) => updateSettings('advanced', 'custom_js', value)}
                    />
                </PanelBody>
            )
        }
    ];

    return (
        <div className="wrap">
            <h1>{__('WordPress Admin Customization', 'wp-react-admin-panel')}</h1>
            {notice && (
                <Notice status={notice.status} onRemove={() => setNotice(null)}>
                    {notice.message}
                </Notice>
            )}
            <Panel>
                <TabPanel
                    className="wp-react-admin-panel-tabs"
                    tabs={tabs}
                >
                    {(tab) => tab.content}
                </TabPanel>
                <PanelBody>
                    <Button
                        isPrimary
                        onClick={saveSettings}
                    >
                        {__('Save Settings', 'wp-react-admin-panel')}
                    </Button>
                </PanelBody>
            </Panel>
        </div>
    );
}

render(<App />, document.getElementById('wp-react-admin-panel'));