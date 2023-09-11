import React, { useEffect, useRef } from 'react'

const apiKey = "AIzaSyCUAevtgJasc6M2mStQScTfvBgfDfvC2go&libraries=places";
const mapApiJs = 'https://maps.googleapis.com/maps/api/js';

function loadAsyncScript(src) { return new Promise(resolve => { const script = document.createElement("script"); Object.assign(script, { type: "text/javascript", async: true, src }); script.addEventListener("load", () => resolve(script)); document.head.appendChild(script) }) }


const AddressLable = ({ handleInputChange, errors, formData ,setFormData }) => {
    const searchInput = useRef(null);
    const initMapScript = () => { if (window.google) return Promise.resolve(); const src = `${mapApiJs}?key=${apiKey}&libraries=places&v=weekly`; return loadAsyncScript(src); }
    const initAutocomplete = () => {
        if (!searchInput.current) return;
        const autocomplete = new window.google.maps.places.Autocomplete(searchInput.current);
        autocomplete.setFields([ "address_component", "geometry", "name" ]);
        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();
            const formattedAddress = place.name;
            setFormData((prevFormData) => ({
                ...prevFormData,
                address: formattedAddress,
            }));
        });
    };
    useEffect(() => { initMapScript().then(() => initAutocomplete()) }, []);
    return (
        <>
            <div className="label">Address</div>
            <img className="addImg" src="/build/images/pin.png" alt="Logo" />
            <input
                ref={searchInput}
                className="addressinput"
                placeholder="Street, building, city, country"
                name="address"
                value={formData.address}
                onChange={handleInputChange}
            />
            {errors.address && <div className="error">{errors.address}</div>}
        </>
    );
};

export default AddressLable;