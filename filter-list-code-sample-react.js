import React from "react";
import { Link, useLocation } from "react-router-dom";
import FilterTableForm from "../../Forms/Input/FilterTableForm";
import { getLocalDate } from "../../../util/helpers";

const FilterListConfig = {
  fiscalYear: {
    isJoin: false,
  },
};

const Columns = [
  {
    Header: "Fiscal Year",
    accessor: "fiscalYear", // accessor is the "key" in the data
    disableSortBy: true,
    filterCode: "fiscalYear",
    isVisible: false,
  },
  {
    Header: "Fiscal Year",
    accessor: "fiscalYearLink", // accessor is the "key" in the data
    disableSortBy: true,
    filterCode: "fiscalYearLink",
  },
  {
    Header: "Fiscal Quarter",
    accessor: "fiscalQuarter", // accessor is the "key" in the data
    disableSortBy: true,
    filterCode: "fiscalQuarter",
  },
  {
    Header: "Status",
    accessor: "status", // accessor is the "key" in the data
    disableSortBy: true,
    filterCode: "status",
  },
  {
    Header: "Reviewed",
    accessor: "reviewed", // accessor is the "key" in the data
    disableSortBy: true,
    filterCode: "reviewed",
  },
];

const ActivityReportList = (props) => {
  const { reportList } = props;
  const location = useLocation();

  const initialItems = reportList.map((activityReport) => {
    const activityReportReviewed = activityReport?.reviewed !== null ? getLocalDate(activityReport.reviewed) : "Pending Review";
    return {
      ...activityReport,
      fiscalYearLink: <Link to={`${location.pathname}/${activityReport.id}`}>{activityReport.fiscalYear}</Link>,
      fiscalYear: activityReport.fiscalYear.toString(),
      reviewed: activityReportReviewed,
    };
  });

  return (
    <>
      {initialItems ? (
        <section id="goal-plan-list">
          <div>{initialItems.length > 0 && <FilterTableForm Columns={Columns} FilterListConfig={FilterListConfig} tableItems={initialItems}></FilterTableForm>}</div>
        </section>
      ) : (
        <span className="loader loader-pulse">Loading activity reports...</span>
      )}
    </>
  );
};

export default ActivityReportList;
