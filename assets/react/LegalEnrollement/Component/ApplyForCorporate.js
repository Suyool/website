import Footer from "./Footer";
import axios from "axios";
import React, { useEffect, useRef, useState } from 'react'
import TypeOfBusiness from "./TypeOfBusiness";
import AddressLable from "./AddressLable";
import MyVerticallyCenteredModal from "./MyVerticallyCenteredModal";
import OwnerInput from "./OwnerInput";
import DatePicker from "react-datepicker";

const ApplyForCorporate = ({ steSent }) => {
    const [getInfoShowing, setInfoShowing] = useState(false);
    const [modalShow, setModalShow] = useState(false);
    const [getModalTitle, setModalTitle] = useState("");
    const [getModalDes, setModalDes] = useState("");
    const [getDropDown, setDropDown] = useState([]);
    const [formData, setFormData] = useState({
        registeredName: "",
        businessType: "",
        phoneNumber: "",
        email: "",
        address: "",
        authorizedPerson: "",
        authorizedPhoneNumber: "",
        contactFullName: "",
        contactPhoneNumber: "",
        contactEmail: "",
    });
    const [errors, setErrors] = useState({
        address: "",
    });
    const [data, setData] = useState([
        { Name: "" }
    ])
    const [startDate, setStartDate] = useState(new Date());

    useEffect(() => {
        axios
            .get("http://10.20.80.62/CorporateAPI/api/v1/MerchantEnrollment/GetCorporateBusinessType")
            .then((response) => {
                setDropDown(response.data)
            })
            .catch((error) => {
                console.log(error);
            });
    }, [])

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData((prevFormData) => ({
            ...prevFormData,
            [name]: value,
        }));
    };

    const handleFormSubmit = (e) => {
        console.log(data);
        e.preventDefault();

        const newErrors = {};

        if (!formData.registeredName.trim()) {
            newErrors.registeredName = "Registered Name is required";
        }

        if (!formData.businessType.trim()) {
            newErrors.businessType = "Type of Business is required";
        } else if (formData.businessType == 0) {
            newErrors.businessType = "You should select one";
        }

        if (!formData.phoneNumber.trim()) {
            newErrors.phoneNumber = "Phone Number is required";
        } else if (formData.phoneNumber.length < 8) {
            newErrors.phoneNumber = "Phone Number must be at least 8 characters";
        }

        if (!formData.email.trim()) {
            newErrors.email = "Email is required";
        } else if (! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
            newErrors.email = "Invalid email format";
        }

        if (!formData.address.trim()) {
            newErrors.address = "Address is required";
        }

        if (!formData.authorizedPerson.trim()) {
            newErrors.authorizedPerson = "Person Authorize is required";
        }

        if (!formData.authorizedPhoneNumber.trim()) {
            newErrors.authorizedPhoneNumber = "Authorized Phone Number is required";
        }

        if (!formData.contactFullName.trim()) {
            newErrors.contactFullName = "Contact Full Name is required";
        }

        if (!formData.contactPhoneNumber.trim()) {
            newErrors.contactPhoneNumber = "Contact Phone Number is required";
        }

        if (!formData.contactEmail.trim()) {
            newErrors.contactEmail = "Contact email is required";
        }
        // console.log(newErrors);

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
        } else {
            setErrors({});
            console.log("Form data:", formData);
            axios
                .post("http://10.20.80.62/CorporateAPI/api/v1/MerchantEnrollment/SaveCorporateOnboardData", {
                    registeredName: formData.registeredName,
                    businessType: formData.businessType,
                    phoneNumber: formData.phoneNumber,
                    email: formData.email,
                    address: formData.address,
                    authorizedPerson: formData.authorizedPerson,
                    authorizedPhoneNumber: formData.authorizedPhoneNumber,
                    contactEmail: formData.contactEmail,
                    contactFullName: formData.contactFullName,
                    contactPhoneNumber: formData.contactPhoneNumber
                })
                .then((response) => {
                    console.log(response);
                    if (response.data.Payload.GlobalCode = 1 && response.data.Payload.FlagCode > 1) {
                        setModalTitle(response.data.Payload.Title);
                        setModalDes(response.data.Payload.Message);
                        setModalShow(true);
                    }
                    if (response.data.Payload.GlobalCode == 0 && response.data.Payload.FlagCode == 0) {
                        steSent(true)
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    };

    const renderLabelAndInput = (labelText, placeholderText, inputName) => {
        return (
            <>
                <div className="label">{labelText}</div>
                <input
                    className="input"
                    placeholder={placeholderText}
                    name={inputName}
                    value={formData[inputName]}
                    onChange={handleInputChange}
                />
                {errors[inputName] && <div className="error">{errors[inputName]}</div>}
            </>
        );
    };

    return (
        <>
            <div className="ApplyForCorporate">
                <div className="CorporateCont">
                    <div className="TopSection">Apply for Corporate Account</div>

                    <div className="formCompany">
                        <div className="title">COMPANY INFORMATION</div>
                        <>
                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Company Name", "Company Registered name", "tst")}</div>
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Company Legal Form", "Drop down + Others", "tst")}</div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12 relativity">
                                    <div className="label">Date of incorporation</div>
                                    <img className="addImgss" src="/build/images/calender.png" />
                                    <DatePicker className="input" selected={startDate} onChange={(date) => setStartDate(date)} />
                                    {errors.authorizedPerson && <div className="error">{errors.authorizedPerson}</div>}
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Registration Number", "123456", "tst")}</div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">
                                    <div className="label">Type of Business</div>
                                    <TypeOfBusiness getDropDown={getDropDown} setFormData={setFormData} formData={formData} handleInputChange={handleInputChange} />
                                    {errors["businessType"] && <div className="error">{errors["businessType"]}</div>}
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Yearly Turnover", "Average", "tst")}</div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Business Phone Number", "+961", "tst")}</div>
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Email", "name@name.com", "tst")}</div>
                            </div>

                            <div className="row">
                                <div className="col-lg-10 col-md-12 col-sm-12 address"><AddressLable handleInputChange={handleInputChange} errors={errors} formData={formData} setFormData={setFormData} />

                                </div>
                            </div>
                        </>

                        <div className="title">OWNERS INFORMATION</div>
                        <>
                            <OwnerInput data={data} setData={setData} />

                            <div className={`row`}>
                                <div className="col-lg-4 col-md-6 col-sm-12 relativity">
                                    <div className="label">Person In charge (Authorized Signatory) </div>
                                    <img className="addImgs" src="/build/images/info.png" onClick={() => setInfoShowing(!getInfoShowing)} alt="Logo" />
                                    <input
                                        className="input"
                                        placeholder="First Name, Father’s Name, Last Name"
                                        name="authorizedPerson"
                                        value={formData.authorizedPerson}
                                        onChange={handleInputChange}
                                    />
                                    {errors.authorizedPerson && <div className="error">{errors.authorizedPerson}</div>}
                                    {getInfoShowing && (
                                        <div className="infoCont">
                                            <div className="titleInf">Authorized Signatory: </div>
                                            <div className="desc">
                                                The person who is allowed to act on behalf of the company. Their name should be mentioned in the official
                                                records.
                                            </div>
                                        </div>
                                    )}
                                </div>
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Mobile Number", "+961", "tst")}</div>
                            </div>
                        </>


                        <div className="title">CONTACT PERSON</div>
                        <>
                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Full Name", "Full Name", "tst")}</div>
                            </div>

                            <div className="row">
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Phone Number", "+961", "tst")}</div>
                                <div className="col-lg-4 col-md-6 col-sm-12">{renderLabelAndInput("Email", "name@name.com", "tst")}</div>
                            </div>
                        </>

                        <MyVerticallyCenteredModal
                            show={modalShow}
                            title={getModalTitle}
                            description={getModalDes}
                            onHide={() => setModalShow(false)}
                        />

                        <Footer handleFormSubmit={handleFormSubmit} />
                    </div>
                </div>
            </div>
        </>
    );
};

export default ApplyForCorporate;

// // import React, { useEffect, useState } from "react";
// import Footer from "./Footer";
// import axios from "axios";
// import Modal from 'react-bootstrap/Modal';
// import React, { useEffect, useRef, useState } from 'react'
// import TypeOfBusiness from "./TypeOfBusiness";

// const apiKey = "AIzaSyCUAevtgJasc6M2mStQScTfvBgfDfvC2go&libraries=places";
// const mapApiJs = 'https://maps.googleapis.com/maps/api/js';

// function loadAsyncScript(src) { return new Promise(resolve => { const script = document.createElement("script"); Object.assign(script, { type: "text/javascript", async: true, src }); script.addEventListener("load", () => resolve(script)); document.head.appendChild(script) }) }


// function MyVerticallyCenteredModal(props) {
//     return (
//         <Modal
//             {...props}
//             size="md"
//             aria-labelledby="contained-modal-title-vcenter"
//             centered
//         >
//             <Modal.Body>
//                 <div id="legalModle">
//                     <img src="/build/images/warning.png" alt="warrning" />
//                     <div className="title">{props.title}</div>
//                     <div className="description">{props.description}</div>
//                     <button className="okiBtnModal" onClick={props.onHide}>ok</button>
//                 </div>
//             </Modal.Body>
//         </Modal>
//     );
// }

// const ApplyForCorporate = ({ steSent }) => {
//     const [getInfoShowing, setInfoShowing] = useState(false);
//     const [modalShow, setModalShow] = useState(false);
//     const [getModalTitle, setModalTitle] = useState("");
//     const [getModalDes, setModalDes] = useState("");
//     const [getDropDown, setDropDown] = useState([]);

//     useEffect(() => {
//         axios
//             .get("http://10.20.80.62/CorporateAPI/api/v1/MerchantEnrollment/GetCorporateBusinessType")
//             .then((response) => {
//                 setDropDown(response.data)
//             })
//             .catch((error) => {
//                 console.log(error);
//             });
//     }, [])

//     const [formData, setFormData] = useState({
//         registeredName: "",
//         businessType: "",
//         phoneNumber: "",
//         email: "",
//         address: "",
//         authorizedPerson: "",
//         authorizedPhoneNumber: "",
//         contactFullName: "",
//         contactPhoneNumber: "",
//         contactEmail: "",
//     });
//     const [errors, setErrors] = useState({
//         address: "",
//     });

//     const handleInputChange = (e) => {
//         const { name, value } = e.target;
//         setFormData((prevFormData) => ({
//             ...prevFormData,
//             [name]: value,
//         }));
//     };

//     const handleFormSubmit = (e) => {
//         e.preventDefault();

//         const newErrors = {};

//         if (!formData.registeredName.trim()) {
//             newErrors.registeredName = "Registered Name is required";
//         }

//         if (!formData.businessType.trim()) {
//             newErrors.businessType = "Type of Business is required";
//         }else if(formData.businessType == 0){
//             newErrors.businessType = "You should select one";
//         }

//         if (!formData.phoneNumber.trim()) {
//             newErrors.phoneNumber = "Phone Number is required";
//         } else if (formData.phoneNumber.length < 8) {
//             newErrors.phoneNumber = "Phone Number must be at least 8 characters";
//         }

//         if (!formData.email.trim()) {
//             newErrors.email = "Email is required";
//         } else if (! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
//             newErrors.email = "Invalid email format";
//         }

//         if (!formData.address.trim()) {
//             newErrors.address = "Address is required";
//         }

//         if (!formData.authorizedPerson.trim()) {
//             newErrors.authorizedPerson = "Person Authorize is required";
//         }

//         if (!formData.authorizedPhoneNumber.trim()) {
//             newErrors.authorizedPhoneNumber = "Authorized Phone Number is required";
//         }

//         if (!formData.contactFullName.trim()) {
//             newErrors.contactFullName = "Contact Full Name is required";
//         }

//         if (!formData.contactPhoneNumber.trim()) {
//             newErrors.contactPhoneNumber = "Contact Phone Number is required";
//         }

//         if (!formData.contactEmail.trim()) {
//             newErrors.contactEmail = "Contact email is required";
//         }

//         if (Object.keys(newErrors).length > 0) {
//             setErrors(newErrors);
//         } else {
//             setErrors({});
//             console.log("Form data:", formData);
//             axios
//                 .post("http://10.20.80.62/CorporateAPI/api/v1/MerchantEnrollment/SaveCorporateOnboardData", {
//                     registeredName: formData.registeredName,
//                     businessType: formData.businessType,
//                     phoneNumber: formData.phoneNumber,
//                     email: formData.email,
//                     address: formData.address,
//                     authorizedPerson: formData.authorizedPerson,
//                     authorizedPhoneNumber: formData.authorizedPhoneNumber,
//                     contactEmail: formData.contactEmail,
//                     contactFullName: formData.contactFullName,
//                     contactPhoneNumber: formData.contactPhoneNumber
//                 })
//                 .then((response) => {
//                     console.log(response);
//                     if (response.data.Payload.GlobalCode = 1 && response.data.Payload.FlagCode > 1) {
//                         setModalTitle(response.data.Payload.Title);
//                         setModalDes(response.data.Payload.Message);
//                         setModalShow(true);
//                     }
//                     if (response.data.Payload.GlobalCode == 0 && response.data.Payload.FlagCode == 0) {
//                         steSent(true)
//                     }
//                 })
//                 .catch((error) => {
//                     console.log(error);
//                 });
//         }
//     };

//     const renderLabelAndInput = (labelText, placeholderText, inputName) => {
//         return (
//             <>
//                 <div className="label">{labelText}</div>
//                 <input
//                     className="input"
//                     placeholder={placeholderText}
//                     name={inputName}
//                     value={formData[inputName]}
//                     onChange={handleInputChange}
//                 />
//                 {errors[inputName] && <div className="error">{errors[inputName]}</div>}
//             </>
//         );
//     };



//     const searchInput = useRef(null);
//     const initMapScript = () => { if (window.google) return Promise.resolve(); const src = `${mapApiJs}?key=${apiKey}&libraries=places&v=weekly`; return loadAsyncScript(src); }
//     const initAutocomplete = () => {
//         if (!searchInput.current) return;
//         const autocomplete = new window.google.maps.places.Autocomplete(searchInput.current);
//         autocomplete.setFields(["address_component", "geometry", "name"]);
//         autocomplete.addListener("place_changed", () => {
//             const place = autocomplete.getPlace();
//             const formattedAddress = place.name;

//             setFormData((prevFormData) => ({
//                 ...prevFormData,
//                 address: formattedAddress,
//             }));
//         });
//     };
//     useEffect(() => { initMapScript().then(() => initAutocomplete()) }, []);

//     return (
//         <>
//             <div className="ApplyForCorporate">
//                 <div className="CorporateCont">
//                     <div className="TopSection">Apply for Corporate Account</div>

//                     <div className="formCompany">
//                         {renderLabelAndInput("Registered Name", "Registered Name", "registeredName")}

//                         <div className="label">Type of Business</div>
//                         <TypeOfBusiness getDropDown={getDropDown} setFormData={setFormData} formData={formData} handleInputChange={handleInputChange} />
//                         {errors["businessType"] && <div className="error">{errors["businessType"]}</div>}

//                         {renderLabelAndInput("Phone Number", "+961", "phoneNumber")}
//                         {renderLabelAndInput("Email", "name@name.com", "email")}

//                         <div className="address">
//                             <div className="label">Address</div>
//                             <img className="addImg" src="/build/images/pin.png" alt="Logo" />
//                             <input
//                                 ref={searchInput}
//                                 className="addressinput"
//                                 placeholder="Street, building, city, country"
//                                 name="address"
//                                 value={formData.address}
//                                 onChange={handleInputChange}
//                             />
//                             {errors.address && <div className="error">{errors.address}</div>}
//                         </div>

//                         <div className={`row ${getInfoShowing && "mt-2"}`}>
//                             <div className="col-lg-5 col-md-6 col-sm-12">
//                                 <div className="label">Person In charge (Authorized Signatory) <img className="addImg" src="/build/images/info.png" onClick={() => setInfoShowing(!getInfoShowing)} alt="Logo" /></div>
//                                 <input
//                                     className="input"
//                                     placeholder="Full Name"
//                                     name="authorizedPerson"
//                                     value={formData.authorizedPerson}
//                                     onChange={handleInputChange}
//                                 />
//                                 {errors.authorizedPerson && <div className="error">{errors.authorizedPerson}</div>}
//                             </div>
//                             {getInfoShowing && (
//                                 <div className="col-lg-4 col-md-6 col-sm-11 infoCont mt-2">
//                                     <div className="titleInf">Authorized Signatory: </div>
//                                     <div className="desc">
//                                         The person who is allowed to act on behalf of the company. Their name should be mentioned in the official
//                                         records.
//                                     </div>
//                                 </div>
//                             )}
//                         </div>

//                         {renderLabelAndInput("Phone Number", "+961", "authorizedPhoneNumber")}

//                         <div className="title">CONTACT PERSON</div>

//                         {renderLabelAndInput("Full Name", "First & last name", "contactFullName")}
//                         {renderLabelAndInput("Phone Number", "+961", "contactPhoneNumber")}
//                         {renderLabelAndInput("Email", "name@name.com", "contactEmail")}


//                         <Footer handleFormSubmit={handleFormSubmit} />
//                     </div>
//                 </div>
//             </div>

//             <MyVerticallyCenteredModal
//                 show={modalShow}
//                 title={getModalTitle}
//                 description={getModalDes}
//                 onHide={() => setModalShow(false)}
//             />

//         </>
//     );
// };

// export default ApplyForCorporate;
