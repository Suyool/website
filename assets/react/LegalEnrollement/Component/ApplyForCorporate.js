import Footer from "./Footer";
import axios from "axios";
import React, { useEffect, useRef, useState } from "react";
import TypeOfBusiness from "./TypeOfBusiness";
import AddressLable from "./AddressLable";
import MyVerticallyCenteredModal from "./MyVerticallyCenteredModal";
import OwnerInput from "./OwnerInput";
import DatePicker from "react-datepicker";
import LegalForm from "./LegalForm";

const ApplyForCorporate = ({ steSent, env }) => {
  const [ getIsLoading, setIsLoading ] = useState(false);
  const [getInfoShowing, setInfoShowing] = useState(false);
  const [modalShow, setModalShow] = useState(false);
  const [getModalTitle, setModalTitle] = useState("");
  const [getModalDes, setModalDes] = useState("");
  const [getDropDown, setDropDown] = useState([]);
  const [getDropDown1, setDropDown1] = useState([]);
  const [formData, setFormData] = useState({
    registeredName: "",
    legalForm: "",
    dateIncorporation: "",
    registrationNumber: "",
    businessType: "",
    yearlyTurnover: "",
    phoneNumber: "",
    email: "",
    address: "",
    authorizedPerson: "",
    authorizedPhoneNumber: "",
    contactEmail: "",
    contactFullName: "",
    contactPhoneNumber: "",
    ownerInfos: [""],
  });

  const [errors, setErrors] = useState({
    address: "",
  });
  const [data, setData] = useState([{ Name: "" }]);
  const [startDate, setStartDate] = useState(new Date());
  let baseUrl;
  if (env === "prod") {
    baseUrl =
      "https://corporateapiservice.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/";
  } else {
    baseUrl = "http://10.20.80.62/CorporateAPI/api/";
  }

  useEffect(() => {
    axios
      .get(`${baseUrl}v1/MerchantEnrollment/GetCorporateBusinessType`)
      .then((response) => {
        setDropDown(response.data);
      })
      .catch((error) => {
        console.log(error);
      });
    axios
      .get(`${baseUrl}v1/MerchantEnrollment/GetCorporateLegalForm`)
      .then((response) => {
        setDropDown1(response.data);
      })
      .catch((error) => {
        console.log(error);
      });
  }, []);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevFormData) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleyearlyTurnoverChange = (e) => {
    const { name, value } = e.target;
    const numericInput = value.replace(/[^0-9]/g, "");
    setFormData((prevFormData) => ({
      ...prevFormData,
      [name]: numericInput,
    }));
  };

  useEffect(() => {
    const errorContainer = document.querySelector(".error-container");
    if (errorContainer) {
      const errorElements = errorContainer.querySelectorAll(".error");
      if (errorElements.length > 0) {
        errorElements[0].scrollIntoView({ behavior: "smooth" });
      }
    }
  }, [errors]);

  const handleFormSubmit = (e) => {
    setIsLoading(true)
    e.preventDefault();

    const newErrors = {};

    if (!formData.registeredName.trim()) {
      newErrors.registeredName = "Company Name is required";
    }

    if (!formData.legalForm.trim()) {
      newErrors.legalForm = "Legal Form is required";
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
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = "Invalid email format";
    }

    if (!formData.address.trim()) {
      newErrors.address = "Address is required";
    }

    if (!formData.registrationNumber.trim()) {
      newErrors.registrationNumber = "Registration Number is required";
    }

    if (!formData.dateIncorporation.trim()) {
      newErrors.dateIncorporation = "Date of Incorporation is required";
    }

    if (!formData.authorizedPerson.trim()) {
      newErrors.authorizedPerson = "Authorized Person is required";
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

    if (!formData.yearlyTurnover.trim()) {
      newErrors.yearlyTurnover = "Yearly Turnover is required";
    }

    if (data.length < 1) {
      newErrors.ownerInfos = "At least one field is required";
    } else if (data.some((item) => item.Name === "")) {
      newErrors.ownerInfos = "At one of these field is empty";
    }

    // console.log(newErrors);

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      setIsLoading(false);
    } else {
      setErrors({});
      const namesArray = data.map((item) => item.Name);
      axios
        .post(`${baseUrl}v1/MerchantEnrollment/SaveCorporateOnboardData`, {
          registeredName: formData.registeredName,
          legalForm: formData.legalForm,
          dateIncorporation: formData.dateIncorporation,
          registrationNumber: formData.registrationNumber,
          businessType: formData.businessType,
          yearlyTurnover: formData.yearlyTurnover,
          phoneNumber: formData.phoneNumber,
          email: formData.email,
          address: formData.address,
          authorizedPerson: formData.authorizedPerson,
          authorizedPhoneNumber: formData.authorizedPhoneNumber,
          contactEmail: formData.contactEmail,
          contactFullName: formData.contactFullName,
          contactPhoneNumber: formData.contactPhoneNumber,
          ownerInfos: namesArray,
        })
        .then((response) => {
          setIsLoading(false)
          if (
            (response.data.Payload.GlobalCode =
              1 && response.data.Payload.FlagCode > 1)
          ) {
            setModalTitle(response.data.Payload.Title);
            setModalDes(response.data.Payload.Message);
            setModalShow(true);
          }
          if (
            response.data.Payload.GlobalCode == 0 &&
            response.data.Payload.FlagCode == 0
          ) {
            steSent(true);
          }
        })
        .catch((error) => {
          setIsLoading(false)
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
      <div className="ApplyForCorporate error-container">
        <div
        className="CorporateCont">
          {getIsLoading && <div className="hideBack"></div>}
          <div className="TopSection">Apply for Corporate Account</div>

          <div className="formCompany">
            <div className="title">COMPANY INFORMATION</div>
            <>
              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Company Name",
                    "Company Registered name",
                    "registeredName"
                  )}
                </div>
                <div className="col-lg-4 col-md-6 col-sm-12">
                  <div className="label">Company Legal Form</div>
                  <LegalForm
                    getDropDown={getDropDown1}
                    setFormData={setFormData}
                    formData={formData}
                    handleInputChange={handleInputChange}
                  />
                  {errors["legalForm"] && (
                    <div className="error">{errors["legalForm"]}</div>
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12 relativity">
                  <div className="label">Date of incorporation</div>
                  <img className="addImgss" src="/build/images/calender.png" />
                  <DatePicker
                    className="input"
                    showYearDropdown
                    dateFormat="MM/dd/yyyy"
                    scrollableYearDropdown
                    yearDropdownItemNumber={100}
                    selected={startDate}
                    maxDate={new Date()}
                    onChange={(date) => {
                      if (date) {
                        setStartDate(date);
                      }
                      setFormData((prevFormData) => ({
                        ...prevFormData,
                        dateIncorporation: date?.toLocaleDateString("en-US"),
                      }));
                    }}
                  />
                  {errors.dateIncorporation && (
                    <div className="error">{errors.dateIncorporation}</div>
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Registration Number",
                    "123456",
                    "registrationNumber"
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  <div className="label">Type of Business</div>
                  <TypeOfBusiness
                    getDropDown={getDropDown}
                    setFormData={setFormData}
                    formData={formData}
                    handleInputChange={handleInputChange}
                  />
                  {errors["businessType"] && (
                    <div className="error">{errors["businessType"]}</div>
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  <div className="label">Yearly Turnover</div>
                  <input
                    className="input"
                    placeholder="Average"
                    inputMode="numeric"
                    pattern="[0-9]*"
                    name={"yearlyTurnover"}
                    value={formData["yearlyTurnover"]}
                    onChange={handleyearlyTurnoverChange}
                  />
                  {errors["yearlyTurnover"] && (
                    <div className="error">{errors["yearlyTurnover"]}</div>
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Business Phone Number",
                    "+961",
                    "phoneNumber"
                  )}
                </div>
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput("Email", "name@name.com", "email")}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-10 col-md-12 col-sm-12 address">
                  <AddressLable
                    handleInputChange={handleInputChange}
                    errors={errors}
                    formData={formData}
                    setFormData={setFormData}
                  />
                </div>
              </div>
            </>

            <div className="title">OWNERS INFORMATION</div>
            <>
              <OwnerInput data={data} setData={setData} />
              {errors["ownerInfos"] && (
                <div className="error">{errors["ownerInfos"]}</div>
              )}

              <div className={`row`}>
                <div className="col-lg-4 col-md-6 col-sm-12 relativity">
                  <div className="label">
                    Person In charge (Authorized Signatory)
                  </div>
                  <img
                    className="addImgs"
                    src="/build/images/info.png"
                    onClick={() => setInfoShowing(!getInfoShowing)}
                    alt="Logo"
                  />
                  <input
                    className="input"
                    placeholder="First Name, Father’s Name, Last Name"
                    name="authorizedPerson"
                    value={formData.authorizedPerson}
                    onChange={handleInputChange}
                  />
                  {errors.authorizedPerson && (
                    <div className="error">{errors.authorizedPerson}</div>
                  )}
                  {getInfoShowing && (
                    <div className="infoCont">
                      <div className="titleInf">Authorized Signatory:</div>
                      <div className="desc">
                        The person who is allowed to act on behalf of the
                        company. Their name should be mentioned in the official
                        records.
                      </div>
                    </div>
                  )}
                </div>
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Mobile Number",
                    "+961",
                    "authorizedPhoneNumber"
                  )}
                </div>
              </div>
            </>

            <div className="title">CONTACT PERSON</div>
            <>
              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Full Name",
                    "Full Name",
                    "contactFullName"
                  )}
                </div>
              </div>

              <div className="row">
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Phone Number",
                    "+961",
                    "contactPhoneNumber"
                  )}
                </div>
                <div className="col-lg-4 col-md-6 col-sm-12">
                  {renderLabelAndInput(
                    "Email",
                    "name@name.com",
                    "contactEmail"
                  )}
                </div>
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
